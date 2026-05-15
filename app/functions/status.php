<?php
function make_status($statut_num) {
    $HTML = "<select style='width: 200px; margin-bottom: 10px;' class='form-control' name='statut'>";
    if ($statut_num == 1) {
        $HTML .= "<option selected value='1'> Administrateur </option>";
    } else {$HTML .= "<option value='1'> Administrateur </option>";}

    if ($statut_num == 2) {
        $HTML .= "<option selected value='2'> Cadre </option>";
    } else {$HTML .= "<option value='2'> Cadre </option>";}

    if ($statut_num == 3) {
        $HTML .= "<option selected value='3'> Employé de soutien </option>";
    } else {$HTML .= "<option value='3'> Employé de soutien </option>";}

    if ($statut_num == 4) {
        $HTML .= "<option selected value='4'> Enseignant </option>";
    } else {$HTML .= "<option value='4'> Enseignant </option>";}

    if ($statut_num == 5) {
        $HTML .= "<option selected value='5'> Professionnel </option>";
    } else {$HTML .= "<option value='5'> Professionnel </option>";}
    $HTML .= "</select>";
    return $HTML;
}