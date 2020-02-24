<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 2020-01-06
 * Time: 15:04
 */

namespace Touge\AdminExamination\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class QuestionFacade.
 *
 * @method static \Touge\AdminExamination\Services\CustomerMemberService paper_groups($user_id)
 * @method static \Touge\AdminExamination\Services\CustomerMemberService member_groups($user_id)
 */
class CustomerMember extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'customer_member';
    }
}