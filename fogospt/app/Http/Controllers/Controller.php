<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function generateMetadata()
    {
        switch (get_class($this)){
            case "App\Http\Controllers\FireController":
                $pageTitle = sprintf( "- Incêndio em %s - ", @$this->fire['location']);
                $title = sprintf( "[%s] Incêndio em %s", date("d-m-Y H:i"), @$this->fire['location']);
                $description = sprintf( "Estado: %s - Meios Humano: %d, Meios Terrestres: %d, Meios Aéreos: %d ", @$this->fire['status'], @$this->fire['man'], @$this->fire['terrain'], @$this->fire['aerial']);
                break;
            default:
                $pageTitle = sprintf( "- %s", $this->pageName);
                $title = sprintf( "- %s", $this->pageName);
                $description = "Vê num mapa o estado dos incêndios florestais em Portugal";
        }

        $metadata = array(
            'pageTitle' => $pageTitle,
            'title' => $title,
            'description' => $description,
            'url' => "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
        );

        return $metadata;
    }
}
