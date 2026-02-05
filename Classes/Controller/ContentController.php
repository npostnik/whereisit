<?php
namespace Npostnik\Whereisit\Controller;

use Psr\Http\Message\ResponseInterface;
use Npostnik\Whereisit\Domain\Repository\ContentRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ContentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var ContentRepository
     */
    protected $contentRepository = null;

    /**
     * @param ContentRepository $testimonialRepository
     */
    public function injectContentRepository(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    public function listAction(): ResponseInterface
    {
        $cTypes = $this->contentRepository->listAllContentTypes();
        $listTypes = $this->contentRepository->listAllPluginTypes();
        $cTypeOptions = [];
        foreach ($cTypes as $cType) {
            $label = $this->getLabelForContentElement($cType['CType']);
            $label.= ' - '.$cType['CType'];
            $cTypeOptions[] = [
                'label' => $label,
                'value' => $cType['CType']
            ];
        }
        $collator = new \Collator('de_DE');
        // Sort by label
        usort($cTypeOptions, static function (array $a, array $b) use ($collator): int {
            return $collator->compare((string)$a['label'], (string)$b['label']);
        });
        $listTypeOptions = [];
        foreach ($listTypes as $listType) {
            $label = $this->getLabelForListType($listType['list_type']);
            $label.= ' - '.$listType['list_type'];
            $listTypeOptions[] = [
                'label' => $label,
                'value' => $listType['list_type']
            ];
        };
        // Sort by label
        usort($listTypeOptions, static function (array $a, array $b) use ($collator): int {
            return $collator->compare((string)$a['label'], (string)$b['label']);
        });
        $this->view->assign('cTypeOptions', $cTypeOptions);
        $this->view->assign('listTypeOption', $listTypeOptions);

        if($this->request->hasArgument('cType')) {
            $selectedCType = $this->request->getArgument('cType');
            $this->view->assign('cType', $selectedCType);
        }

        if($this->request->hasArgument('listType')) {
            $selectedListType = $this->request->getArgument('listType');
            $this->view->assign('listType', $selectedListType);
        }

        if(empty($selectedCType) && empty($selectedListType)) {
            $this->view->assign('message', 'Bitte wählen Sie eine Option aus.');
        } elseif(!empty($selectedCType)) {
            $contentElements = $this->contentRepository->findByCType($selectedCType);
            $this->view->assign('contentElements', $contentElements);
        } elseif(!empty($selectedListType)) {
            $contentElements = $this->contentRepository->findByListType($selectedListType);
            $this->view->assign('contentElements', $contentElements);
        } else {
            $this->view->assign('message', 'Bitte wählen Sie nur eine Option aus.');
        }

        return $this->htmlResponse();
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
