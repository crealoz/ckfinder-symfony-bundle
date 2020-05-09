<?php


namespace CKSource\CKFinder\Controller;

use CKSource\CKFinder\Response\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AjaxController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param string $type
     * @return Response
     * @throws \Exception
     */
    public function downloadFileAction(Request $request, SluggerInterface $slugger, string $type): Response
    {
        if ($request->isMethod('POST')){
            /** @var UploadedFile $image */
            $image = $request->files->get('upload');
            if (!$image->isValid()) {
                throw new \Exception($this->translator->trans($image->getErrorMessage()));
            }
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
            try {
                $image->move(
                    $this->getParameter('ckfinder.config.uploadDir'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }

        return JsonResponse::create(['success' => 1]);
    }
}