<?php
/**
 * User: Marc MacLeod
 * Date: 8/3/11
 * Time: 10:20 PM
 */
 
namespace Marbemac\ImageBundle\Extension;

class MarbemacImageTwigExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
        );
    }

    public function getFunctions() {
        return array(
            'imageDataEncode'  => new \Twig_Function_Method($this, 'imageDataEncode')
        );
    }

    /*
     * Returns an encoded string that holds the image data payload.
     */
    public function imageDataEncode($groupId, $w, $h)
    {
        return base64_encode($groupId.'-'.$w.'-'.$h);
    }

    public function getName()
    {
        return 'marbemac_image_twig_extension';
    }
}