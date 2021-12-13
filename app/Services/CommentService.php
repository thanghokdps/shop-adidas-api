<?php

namespace App\Services;

use App\Repositories\CommentRepository;

class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function all()
    {
        return $this->commentRepository->all();
    }

    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->commentRepository->findByField($field, $value, $columns);
    }

    public function create(array $attributes)
    {
        return $this->commentRepository->create($attributes);
    }

    public function update(array $attributes, $id)
    {
        return $this->commentRepository->update($attributes, $id);
    }

    public function getAccordingToStar($id, $star)
    {
        return $this->commentRepository->findWhere(['product_id' => $id, 'star' => $star]);
    }
}
