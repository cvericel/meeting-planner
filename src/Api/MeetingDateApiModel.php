<?php


namespace App\Api;


class MeetingDateApiModel
{
    public $id;

    public $start_at;

    public $end_at;

    public $meeting;

    private $links = [];

    public function addLink($ref, $url) : void
    {
        $this->links[$ref] = $url;
    }

    public function getLink() : array
    {
        return $this->links;
    }
}