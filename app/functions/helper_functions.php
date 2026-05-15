<?php
/**
 * Helper function for `htmlspecialchars`.
 * @return `null` if the passed value is null, else the value with `htmlspecialchars`.
 */
function h_hsc($value, $filter) {
    return $value == null ? null : htmlspecialchars($value, $filter);
}