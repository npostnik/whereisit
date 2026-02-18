<?php
namespace Npostnik\Whereisit\Controller;

use Psr\Http\Message\ResponseInterface;
use Npostnik\Whereisit\Domain\Repository\ContentRepository;
use Npostnik\Whereisit\Domain\Repository\PageRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ContentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var ContentRepository
     */
    protected $contentRepository = null;

    /**
     * @var PageRepository
     */
    protected $pageRepository = null;

    /**
     * @var array
     */
    protected $pages;

    /**
     * @param ContentRepository $contentRepository
     */
    public function injectContentRepository(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    /**
     * @param PageRepository $pageRepository
     */
    public function injectPageRepository(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function listAction(): ResponseInterface
    {
        $cTypes = $this->contentRepository->listAllContentTypes();
        $this->pages = $this->pageRepository->findAll();

        $cTypeOptions = $this->buildGroupedCTypeOptions($cTypes);
        $this->view->assign('cTypeOptions', $cTypeOptions);

        if($this->request->hasArgument('cType')) {
            $selectedCType = $this->request->getArgument('cType');
            $this->view->assign('cType', $selectedCType);
        }

        if(empty($selectedCType) && empty($selectedListType)) {
            $this->view->assign('message', 'Bitte wählen Sie eine Option aus.');
        } elseif(!empty($selectedCType)) {
            $contentElements = $this->contentRepository->findByCType($selectedCType);
            foreach ($contentElements as &$contentElement) {
                $pageRecord = $this->getPageRecord($contentElement['pid']);
                if($pageRecord) {
                    $contentElement['pageTitle'] = $pageRecord['title'];
                    $contentElement['slug'] = $pageRecord['slug'];
                }
            }
            $this->view->assign('contentElements', $contentElements);
        } else {
            $this->view->assign('message', 'Bitte wählen Sie nur eine Option aus.');
        }

        return $this->htmlResponse();
    }

    protected function getPageRecord($pid)
    {
        foreach ($this->pages as $page) {
            if($page['uid'] === $pid) {
                return $page;
            }
        }
    }

    protected function buildGroupedCTypeOptions(array $cTypes): array
    {
        $tcaConfig = $GLOBALS['TCA']['tt_content']['columns']['CType']['config'];
        $itemGroups = $tcaConfig['itemGroups'] ?? [];
        $items = $tcaConfig['items'] ?? [];

        // Build lookup: value => group
        $groupLookup = [];
        foreach ($items as $item) {
            if (!empty($item['value'])) {
                $groupLookup[$item['value']] = $item['group'] ?? '';
            }
        }

        // Resolve group labels
        $groupLabels = [];
        foreach ($itemGroups as $groupId => $groupLabel) {
            if (str_starts_with($groupLabel, 'LLL:')) {
                $groupLabels[$groupId] = LocalizationUtility::translate($groupLabel) ?: $groupId;
            } else {
                $groupLabels[$groupId] = $groupLabel;
            }
        }

        // Group the actually used CTypes
        $grouped = [];
        foreach ($cTypes as $cType) {
            $value = $cType['CType'];
            $label = $this->getLabelForContentElement($value);
            $group = $groupLookup[$value] ?? '';

            if (!isset($grouped[$group])) {
                $grouped[$group] = [
                    'label' => $groupLabels[$group] ?? $group ?: 'Sonstige',
                    'items' => [],
                ];
            }
            $grouped[$group]['items'][] = [
                'label' => $label . ' - ' . $value,
                'value' => $value,
            ];
        }

        // Sort items within each group
        $collator = new \Collator('de_DE');
        foreach ($grouped as &$group) {
            usort($group['items'], static function (array $a, array $b) use ($collator): int {
                return $collator->compare((string)$a['label'], (string)$b['label']);
            });
        }

        // Sort groups by itemGroups order from TCA
        $sortedGroups = [];
        foreach (array_keys($itemGroups) as $groupId) {
            if (isset($grouped[$groupId])) {
                $sortedGroups[] = $grouped[$groupId];
                unset($grouped[$groupId]);
            }
        }
        // Append any remaining groups not defined in itemGroups
        foreach ($grouped as $group) {
            $sortedGroups[] = $group;
        }

        return $sortedGroups;
    }

    protected function getLabelForContentElement($cType)
    {
        $types = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'];
        $label = '';
        foreach ($types as $type) {
            if($type['value'] === $cType) {
                $label = $type['label'];
            }
        }

        if(str_starts_with($label, 'LLL')) {
            return LocalizationUtility::translate($label);
        }

        if(!empty($label)) {
            return $label;
        }

        return $cType;
    }

    protected function getLabelForListType($listType)
    {
        $types = $GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'];
        $label = '';
        foreach ($types as $type) {
            if($type['value'] === $listType) {
                $label = $type['label'];
            }
        }

        if(str_starts_with($label, 'LLL:')) {
            try {
                return LocalizationUtility::translate($listType);
            } catch(\Exception $e) {
                return $listType;
            }
        }

        if(!empty($label)) {
            return $label;
        }

        return $listType;
    }



}
