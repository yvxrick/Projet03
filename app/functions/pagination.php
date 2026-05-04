<?php
/**
 * Fait la pagination pour le menu principal des annonces.
 * @param int $num_total_pages Le nombre total de pages
 * @return void
 */
function make_pagination_annonces($num_total_pages)
{
    $current_page = $_GET["page"] ?? null;
    $current_page == 1 ? $pagination = "<input class='btn btn-secondary' disabled type='button' value='<' onclick='setPage(-1, false)'>" : $pagination = "<input class='btn btn-secondary' type='button' value='<' onclick='setPage(-1, false)'>";
    for ($i = 0; $i < $num_total_pages; $i++) {
        $n = $i + 1;
        $n == $current_page ? $pagination .= "<input class='btn btn-secondary active' type='button' value='$n' onclick='setPage($n, true)'>" : $pagination .= "<input class='btn btn-secondary' type='button' value='$n' onclick='setPage($n, true)'>";
    }

    $current_page == $num_total_pages ? $pagination .= "<input class='btn btn-secondary' disabled type='button' value='>' onclick='setPage(1, false)'>" : $pagination .= "<input class='btn btn-secondary' type='button' value='>' onclick='setPage(1, false)'>";
    $pagination .= "<input id='specific_page' onchange='setSpecificPage()' style='width: 100px; font-size: 12px;' type='text' placeholder='Page spécifique'>";
    echo $pagination;
    echo '<script> function setPage(page, specific) {
            if (specific) {
                URLParams.set("page", page)
                location.search = URLParams
                return;
            }
            p = parseInt(URLParams.get("page")) + page
            URLParams.set("page", p)
            location.search = URLParams
        } 
        function setSpecificPage() {
            let p = parseInt(document.getElementById("specific_page").value)
            if (isNaN(p)) {alert("Veuillez entrer une numéro de page valide."); return;}
            URLParams.set("page", p)
            location.search = URLParams
        }
        </script>';
}