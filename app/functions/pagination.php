<?php
/**
 * Fait la pagination pour le menu principal des annonces.
 * @param int $nb_pages
 * @return void
 */
function make_pagination_annonces($nb_pages)
{
    $current_page = $_GET["page"] ?? null;
    $current_page == 1 ? $pagination = "<input class='btn btn-secondary' disabled type='button' value='<' onclick='setPage(-1, false)'>" : $pagination = "<input class='btn btn-secondary' type='button' value='<' onclick='setPage(-1, false)'>";
    for ($i = 0; $i <= $nb_pages; $i++) {
        $n = $i + 1;
        $n == $current_page ? $pagination .= "<input class='btn btn-secondary active' type='button' value='$n' onclick='setPage($n, true)'>" : $pagination .= "<input class='btn btn-secondary' type='button' value='$n' onclick='setPage($n, true)'>";
    }

    $current_page == $nb_pages + 1 ? $pagination .= "<input class='btn btn-secondary' disabled type='button' value='>' onclick='setPage(1, false)'>" : $pagination .= "<input class='btn btn-secondary' type='button' value='>' onclick='setPage(1, false)'>";
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
        } </script>';
}