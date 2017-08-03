<?php
namespace WPGodWpseopress\Models\Specification;


/**
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @author Thomas DENEULIN <contact@wp-god.com> 
 */
interface SpecificationInterface
{
    /**
     * @version 1.0.0
     * @since 1.0.0
     *
     *
     * @return bool
     */
    public function isSatisfiedBy($item);

    /**
     * @version 1.0.0
     * @since 1.0.0
     *
     * @param SpecificationInterface $spec
     */
    public function andX(SpecificationInterface $spec);

    /**
     * @version 1.0.0
     * @since 1.0.0
     *
     * @param SpecificationInterface $spec
     */
    public function orX(SpecificationInterface $spec);

    /**
     * @version 1.0.0
     * @since 1.0.0
     */
    public function notX();
}