<?php
/**
 */
class Comments_Model_CommentTable extends Doctrine_Table
{
    /*
     * find all comments by entity id param as query
     *
     * @return Doctrine_Query
     */
    public function findAllByEntityIdAsQuery($enityId)
    {
        return $this->createQuery('c')
            ->where('c.entity_id = ?', $enityId);
    }

    public function getLastCommentsInCategory($categoryId)
    {
        $comments = $this->createQuery('comments')
                         ->leftJoin('comments.CommentedInstruction commented_instruction')
                         ->leftJoin('commented_instruction.InstructionCategory instruction_category')
                         ->where('instruction_category.category_id = ?', $categoryId)
                         ->orderBy('comments.created_at DESC')
                         ->limit(5)
                         ->execute();
        return $comments;

    }
}