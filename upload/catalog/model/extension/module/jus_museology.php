<?php
/*
 * This file is part of JusMuseology module for OC3.x
 * (c) 2021 jigius@gmail.com
 */

/**
 * class ModelExtensionModuleJusWishlist
 * Implements a model for the storing of Wish List into the session which is used for anonymous users
 */
final class ModelExtensionModuleJusMuseology extends Model
{
    /**
     * Cntr
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
    }

    /**
     * @inheritDoc
     */
    public function addWishlist($product_id)
    {
        $this->validate();
        $this->session->data['wishlist'][] = $product_id;
        $this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);
    }

    /**
     * @inheritDoc
     */
    public function deleteWishlist($product_id)
    {
        $this->validate();
        if (($idx = array_search($product_id, $this->session->data['wishlist'])) !== false) {
            unset($this->session->data['wishlist'][$idx]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getWishlist()
    {
        $this->validate();
        return
            array_map(
                function ($id) {
                    return array('product_id' => $id);
                },
                $this->session->data['wishlist']
            );
    }

    /**
     * @inheritDoc
     */
    public function getTotalWishlist() {
        $this->validate();
        return count($this->session->data['wishlist']);
    }

    /**
     * Validates the session's stuff
     */
    private function validate()
    {
        if (!isset($this->session->data['wishlist'])) {
            $this->session->data['wishlist'] = [];
        }
    }
}
