<?php
namespace Npostnik\Whereisit\Controller;

use Psr\Http\Message\ResponseInterface;
use Npostnik\Whereisit\Domain\Repository\ContentRepository;

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
            $cTypeOptions[] = [
                'label' => $cType['CType'],
                'value' => $cType['CType']
            ];
        }
        $listTypeOptions = [];
        foreach ($listTypes as $listType) {
            $listTypeOptions[] = [
                'label' => $listType['list_type'],
                'value' => $listType['list_type']
            ];
        };
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


}
