<?php
namespace Ihsan\SimpleAdminBundle;

/**
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: http://blog.khodam.org
 */
class IhsanSimpleCrudEvents
{
    const PRE_PERSIST_EVENT = 'ihsan.simple_admin.pre_persist_event';

    const POST_FLUSH_EVENT = 'ihsan.simple_admin.post_flush_event';

    const PRE_SHOW_RENDER_EVENT = 'ihsan.simple_admin.pre_show_event';

    const PRE_LIST_RENDER_EVENT = 'ihsan.simple_admin.pre_list_event';
}
