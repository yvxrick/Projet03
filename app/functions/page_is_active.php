<?php
/**
 * Function for dynamic navbars.
 */
function page_active($page, $current) {
    return $current === $page ? 'active' : '';
}

/**
 * Returns the `selected` operator when the ad state equals the passed ad state.
 * @param mixed $state_num
 * @param mixed $ad
 * @return string
 */
function is_ad_state($state_num, $ad) {
    return $state_num === intval($ad["Etat"]) ? "selected " : "";
}