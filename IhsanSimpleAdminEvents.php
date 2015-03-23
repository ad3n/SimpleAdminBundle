<?php
namespace Ihsan\SimpleAdminBundle;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */
class IhsanSimpleAdminEvents
{
    const PRE_FORM_CREATE_EVENT = 'ihsan.simple_admin.pre_form_create_event';//GetFormResponseEvent

    const PRE_FORM_SUBMIT_EVENT = 'ihsan.simple_admin.pre_form_submit_event';//GetFormResponseEvent

    const PRE_FORM_VALIDATION_EVENT = 'ihsan.simple_admin.pre_form_validation_event';//GetResponseEvent

    const PRE_SAVE_EVENT = 'ihsan.simple_admin.pre_save_event';//GetEntityResponseEvent

    const POST_SAVE_EVENT = 'ihsan.simple_admin.post_save_event';//GetEntityEvent

    const FILTER_LIST_EVENT = 'ihsan.simple_admin.filter_list_event';//GetQueryEvent

    const PRE_DELETE_EVENT = 'ihsan.simple_admin.pre_delete_event';//GetEntityResponseEvent
}
