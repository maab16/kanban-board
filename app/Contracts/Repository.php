<?php

namespace App\Contracts;

interface Repository
{
    /**
     * Get posts.
     * 
     * @param   array $options
     * @param   array $relations
     * 
     * @return array
     * 
     */
    public static function get($options = [], $relations = []): array;

    /**
     * Get a post.
     * 
     * @param   integer $id
     * @param   array $realtions
     * 
     * @return array
     * 
     */
    public static function first($id, $realtions = []): array;

    /**
     * Search posts.
     * 
     * @param   integer $id
     * @param   array $realtions
     * @param   array $options
     * 
     * @return array
     * 
     */
    public static function search($id, $realtions = [], $options = []): array;

    /**
     * Create a post.
     * 
     * @param   array|object $data
     * 
     * @return array
     * 
     */
    public static function save($data): array;

    /**
     * Update a post.
     * 
     * @param   integer $id
     * @param   array|object $data
     * 
     * @return array
     * 
     */
    public static function update($id, $data): array;

    /**
     * Delete a post.
     * 
     * @param   integer $id
     * 
     * @return array
     * 
     */
    public static function delete($id): array;

}