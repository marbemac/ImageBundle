<?php

namespace Marbemac\ImageBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\DependencyInjection\ContainerAware,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\Security\Core\Exception\AccessDeniedException,
    Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ImageController extends ContainerAware
{
    public function showAction($imageData)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->setVary(array('Accept-Encoding', 'User-Agent'));
        $response->setCache(array(
            'etag' => $imageData,
            'public' => true
        ));

        if ($response->isNotModified($this->container->get('request'))) {
            // return the 304 Response immediately
            return $response;
        }

        // [0] groupid
        // [1] width
        // [2] height
        $parts = explode('-', base64_decode($imageData));

        $image = $this->container->get('marbemac.manager.image')->findOrCreate($parts[0], $parts[1], $parts[2]);
        if ($image && is_object($image))
        {
            $response->setContent($image->getFile()->getBytes());
        }
        else
        {
            $response = new Response('Page not found.', 404);
        }

        return $response;
    }
}