<?php
namespace Admin;
ini_set('max_execution_time', '300');
ini_set('session.gc_maxlifetime', 14400);
session_set_cookie_params(14400);
session_start();

include_once "autoload.php";
include_once "GroupController.php";
include_once "DataSettings.php";
include_once "InvController.php";
include_once "ItemController.php";
include_once "DashboardController.php";
include_once "UserController.php";
include_once "ImportProduct.php";
include_once "Report.php";
include_once "Assignment.php";
include_once "Model/CicModel.php";

use DataSys\DataSettings;
use Group\GroupController;
use Inventory\InvController;
use Item\ItemController;
use Dashboard\DashboardController;
use Report\Report;
use User\UserController;
use COVID19\ImportController;
use Assignment\Assignment;
use CicModel\CicModel;

use PDO;
use stdClass;

class AdminController {

    public static function Portal(){

        switch ($_POST['action']) {

            case 'change_pass':
                UserController::CHECK_USER_PASSWORD();
            break;

            case 'check_holiday':
                GroupController::CHECK_HOLIDAY();
            break;
            case 'group_delete':
                DataSettings::DELETE_GROUP();
            break;
            case 'holiday_delete':
                DataSettings::DELETE_HOLIDAY();
            break;
            case 'check_duplicate_entry':
                ItemController::CHECK_ITEM_DUPLICATE_ENTRY();
            break;
            case 'check_group_isused_bybranch':
                GroupController::CHECK_BRANCH_GROUP();
            break;
            case 'export_ledger':
                CicModel::EXPORT_EXCEL();
                break;
            case 'assignment_manage':
                 Assignment::MANAGE_ASSIGNMENT();
                break;
            case 'assignment':
                Assignment::LOAD_POS_USERS();
                break;
            case 'get_dw_dbdate':
                InvController::LOAD_DB_DATE();
                break;
            case 'remarks_managerecord':
                InvController::MANAGE_REMARKS();
                break;
            case 'remarks_get':
                InvController::GET_REMARKS();
                break;
            case 'import_store_settings':
                 InvController::LOAD_PRODUCT_DATA();
                break;
            case 'import_store_settings_V2':
                InvController::LOAD_PRODUCT_DATA_V2();
                break;
            case 'inventory':
                 // InvController::LOAD_PRODUCT_DATA();
                 InvController::LOAD_INVENTORY();
                break;
            case 'group':
                GroupController::LOAD_BATCH();
                break;
            case 'group_cancel_batch':
                GroupController::CANCEL_BATCH();
                break;
            case 'group_branch':
                GroupController::LOAD_BRANCH();
                break;
            case 'uploadfile':
                GroupController::INITIALIZE_VARIABLES_CUSTOM();
                GroupController::UPLOAD_FILE();
                GroupController::SCHEDULE_PRODUCTS();
                break;
            case 'groupchild':
                GroupController::LOAD_CHILD();
                break;
            case 'load_group_settings':
                DataSettings::GET_GROUP();
                break;
                
            case 'load_group_settings_v2':
                DataSettings::GET_GROUP_V2();
                break;
            case 'load_holiday_settings':
                DataSettings::GET_HOLIDAY();
                break;
            case 'manage_record_settings':
                DataSettings::MANAGE_RECORD();
                break;
            case 'manage_settings':
                DataSettings::MANAGE_SETTINGS();
                break;
            case 'inventory_update':
                InvController::UPDATE_INVENTORY();
                break;
            case 'inventory_post':
                InvController::UPDATE_STATUS();
                break;
            case 'inventory_approve':
                InvController::UPDATE_STATUS();
                break;
            case 'item_approve':
                InvController::UPDATE_STATUS();
                break;
            case 'item':
                ItemController::LOAD_ITEM();
                break;
            case 'item_search':
                ItemController::SEARCH_ITEM();
                break;
            case 'item_child':
                ItemController::CHILD_ITEM();
                break;
            case 'item_cancel':
                ItemController::UPDATE_STATUS();
                break;
            case 'item_cancel_row':
                ItemController::CANCEL_ITEM();
                break;
            case 'item_add_schedule':
                ItemController::ADD_ITEM();
                break;
            case 'item_add_schedule_phaseout':
                ItemController::ADD_PHASEOUT_ITEM();
                break;
            case 'item_branch_group':
                ItemController::LOAD_BRANCH_GROUP();
                break;
            case 'active_group':
                ItemController::LOAD_ACTIVE_GROUP();
                break;
            case 'item_reason':
                ItemController::INSERT_REASON();
            break;
            case 'index':
                DashboardController::LOAD_DASHBOARD();
                break;
            case 'index_ledger':
                DashboardController::LOAD_LEDGER();
                break;
            case 'index_currentcount':
                DashboardController::LOAD_CURRENTCOUNT();
                break; 
            case 'user':
                UserController::LOAD_HO_USERS();
                break;
            case 'user_division':
                UserController::LOAD_USER_DIVISION();
                break;
            case 'user_role':
                DataSettings::GET_ROLE();
                break;
            case 'user_category':
                DataSettings::GET_CATEGORY();
                break;
            case 'user_branch':
                DataSettings::GET_STORE();
                break;
            case 'user_department':
                DataSettings::LOAD_USER_DEPARTMENT();
                break;
            case 'user_managerecord':
                UserController::USER_MANAGERECORD();
                break;
            case 'import_data':
                ImportController::IMPORT_PRODUCT_TO_MYSQL();
                break;
            case 'user_authenticate':
                UserController::AUTHENTICATE_LOGIN();
                break;
            case 'user_check':
                UserController::LOAD_USER();
                break;
            case 'user_logout':
                UserController::LOGOUT_USER();
                break;
            case 'item_category':
                DataSettings::GET_CATEGORY();
                break;
            case 'item_supplier':
                DataSettings::GET_SUPPLIER();
                break;
            case 'item_supplier2':
                DataSettings::GET_SUPPLIER2();
                break;
            case 'rpt_schedule':
                Report::PRODUCT_SCHEDULE();
                break;
            case 'rpt_scheduled_group':
                Report::SCHEDULED_GROUP();
                break;
            case 'role':
                DataSettings::LOAD_ROLE();
            break;
            case 'division':
                DataSettings::LOAD_DIVISION();
            break;
            case 'status':
                DataSettings::LOAD_STATUS();
            break;
            case 'brand':
                DataSettings::LOAD_BRAND();
            break;
            case 'database':
                InvController::LOAD_DATABASE_LOG();
            break;
            case 'database_update':
                InvController::LOAD_DATABASE_CREDENTIALS();
            break;
            default:
        }
    }

   
}

$Admin = new AdminController();
$Admin->Portal();

?>