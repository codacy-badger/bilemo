<?php

namespace App\Domain\Helpers;

use Symfony\Component\HttpFoundation\Request;

final class PaginationHelper
{

    /**
     * @param Request $request
     * @param array $data
     * @param int $limit
     * @return array|int
     */
    public function checkPage(Request $request, array $data, int $limit)
    {
        $page = $request->query->getInt('page');

        if (is_null($page) || $page < 1) {
            $page = 1;
        }

        if ($page > $this->getPages($data, $limit)) {
            return $this->pageDontExist();
        }

        return $page;
    }

    /**
     * @return array
     */
    public function pageDontExist(): array
    {
        return [
            "status" => "404 Ressource introuvable",
            "message" => "Liste des produits introuvable !"
        ];
    }

    /**
     * @param array $data
     * @param int $limit
     * @return int
     */
    public function getPages(array $data, int $limit): int
    {
        $count = count($data);

        return (int)ceil($count / $limit);
    }
}