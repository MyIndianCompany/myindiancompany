<?php

namespace App\Common\Constants;

class Constants
{
    const USER_AGENT = 'agent';
    const USER_CUSTOMER = 'customer';
    const USER_ADMIN = 'admin';

    //Service Category File Path
    const SERVICE_FILE_PATH = "/service";
    const SERVICE_DETAIL_FILE_PATH = "/service/details";
    const SERVICE_CATEGORY_FILE_PATH = "/service_category";
    const SERVICE_CATEGORY_SLIDER_FILE_PATH = "/service_category/slider";

    //Agent Document
    const AGENT_PAN_CARD = "/agent/pan_card";
    const AGENT_PROFILE_PICTURE = "/agent/profile_picture";

    //Category Files Uploading Type
    const THUMBNAIL = "thumbnail";
    const SLIDER = "slider";

    //Status
    const STATUS_ACTIVE = "active";
    const STATUS_INACTIVE = "inactive";
}
