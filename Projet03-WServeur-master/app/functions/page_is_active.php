<?php
/**
 * Function for dynamic navbars.
 */
function page_active($page, $current) {
    return $current === $page ? 'active' : '';
}