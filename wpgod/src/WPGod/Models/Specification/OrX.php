<?php
namespace WPGodWpseopress\Models\Specification;

use WPGodWpseopress\Models\Specification\AbstractSpecification;

/**
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @author Thomas DENEULIN <contact@wp-god.com> 
 */
class OrX extends AbstractSpecification
{

    protected $left;

    protected $right;

    /**
     *
     * @param SpecificationInterface $left
     * @param SpecificationInterface $right
     */
    public function __construct(SpecificationInterface $left, SpecificationInterface $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     *
     * @param $item
     *
     * @return bool
     */
    public function isSatisfiedBy($item)
    {
        return $this->left->isSatisfiedBy($item) || $this->right->isSatisfiedBy($item);
    }
}