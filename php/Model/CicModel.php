<?php

namespace CicModel;
use PDO;
use stdClass;
use Settings\AppSettings;


class CicModel{

    ////////////////////////////////////////////////////////////////////////////////////////
    // VARIABLES
    ////////////////////////////////////////////////////////////////////////////////////////

    public static $DATA_CONTAINER;
    public static $PARAMETER;
    public static $BOOLEAN;

    ////////////////////////////////////////////////////////////////////////////////////////
    // SQL QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////
    // REPORT MODULE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    public static $SQL_GROUP_DELETE = "DELETE FROM sys_group WHERE GROUPID = :GROUPID";
    public static $SQL_HOLIDAY_DELETE = "
        DELETE FROM sys_holiday WHERE holidayid = :HOLIDAYID;

        UPDATE sys_activedate
        SET recordstatus = 4
        WHERE datedesc = :HOLIDAYDATE;
        ";

    public static $SQL_PRODUCT_SCHEDULE_GET = "
    SELECT 
        INS.scheduleid,INS.scheduledate,sellingarea,groupname,statusname,INP.productid,
        INB.storeid,INB.storename,INS.statusid,remarks,tlremarks,atlremarks,
        CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
        CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
        CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) AS onhand,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) AS sellout,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) AS receiving,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) AS adjustment,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) AS sellingarea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) AS warehousearea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) AS variance,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) AS adj_issue,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) AS adj_receipt,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) AS rcv_ir,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) AS rcv_er,
        CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
        'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) END AS cic
	FROM 
		inv_schedule  INS
	INNER JOIN sys_status SS ON SS.StatusId = INS.statusid 
	INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
	INNER JOIN inv_product INP ON INP.productid = INS.productid
	INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
	LEFT JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode
	WHERE INS.statusid =  IFNULL(:STATUSID,INS.`statusid`)
	AND `scheduledate` BETWEEN :DATEFROM AND :DATETO
	AND INB.`groupid` = IFNULL(:GROUPID,INB.`groupid`)
    AND INB.`storeid` = IFNULL(:STOREID,INB.`storeid`)
    ORDER BY cic,groupname,INPD.divisionname,description ASC
    ";

    public static $SQL_SCHEDULED_GROUP_GET = "
    SELECT 
        INS.scheduleid,INS.scheduledate,sellingarea,groupname,statusname,INP.productid,
        INB.storeid,INB.storename,INS.statusid,remarks,tlremarks,atlremarks,
        CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
        CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
        CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) AS onhand,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) AS sellout,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) AS receiving,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) AS adjustment,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) AS sellingarea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) AS warehousearea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) AS variance,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) AS adj_issue,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) AS adj_receipt,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) AS rcv_ir,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) AS rcv_er,
        CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
        'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) END AS cic
	FROM 
		inv_schedule  INS
	INNER JOIN sys_status SS ON SS.StatusId = INS.statusid 
	INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
	INNER JOIN inv_product INP ON INP.productid = INS.productid
	INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
	LEFT JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode
	WHERE INS.statusid =  IFNULL(:STATUSID,INS.`statusid`)
	AND INB.`groupid` = IFNULL(:GROUPID,INB.`groupid`)
    AND INB.`storeid` = IFNULL(:STOREID,INB.`storeid`)
    ORDER BY cic,groupname,INPD.divisionname,description ASC
    ";

    ////////////////////////////////////////////////////////////////////////////////////////
    // ITEM MODULE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    public static $SQL_ADD_FILTER = "
    
        SELECT BARCODE FROM inv_schedule INS
        INNER JOIN inv_product INP ON INP.productid = INS.productid
        INNER JOIN inv_batch INB ON INB.BATCHID = INS.BATCHID
        WHERE BARCODE = :BARCODE
        AND INP.groups = :GROUPID
        AND INS.scheduledate = :SCHEDULEDATE
        AND INB.storeid = :STOREID
        AND INS.statusid = 1
            
    ";

    public static $SQL_ITEM_GET = "
    select INPD.barcode,INPD.description,INPD.supplierid,INPD.suppliername,INPD.supplierid,
    INPD.suppliername,INPD.divisionid,INPD.divisionname ,brandid,brandname
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description
     from inv_product_dw INPD
    where INPD.divisionid = IFNULL(:DIVISIONID,INPD.divisionid)
    and INPD.brandid = IFNULL(:BRANDID,INPD.brandid)
    and INPD.barcode like  CONCAT('%', :BARCODE, '%')
    and INPD.description like CONCAT('%', :DESCRIPTION, '%')
    ";

    public static $SQL_ACTIVE_GROUP_GET  = "
        
        SELECT INB.groupid,SG.groupname from inv_batch INB
        inner join sys_group SG ON SG.groupid = INB.groupid 
        where storeid = :STOREID
        and INB.recordstatus in (1,2)
        and (:PARAMDATE) BETWEEN STARTDATE AND ENDDATE
        group by groupid
        
    ";

    public static $ORA_ITEM_GET = "
    SELECT 
        MSI.BARCODE,
        MSI.LEGACYBARCODE,
        MSI.DESCRIPTION,
        AP.SEGMENT1 AS SUPPLIERID,
        AP.VENDOR_NAME as SUPPLIERNAME,
        MSI.DIVISIONID as DIVISIONID,
        MSI.DIVISIONNAME as DIVISIONNAME,
        MSI.BRANDID AS BRANDID,
        MSI.BRANDNAME AS BRANDNAME,
        MSI.ITEMTYPE AS ITEMTYPE,
        MSI.ITEMTYPENAME AS ITEMTYPENAME,
        MTL.ORGANIZATION_ID AS STOREID
    FROM 
        MTL_SYSTEM_ITEMS_B MTL,
        XXCH_MTLCAT_PRODUCT_V MSI,
        APPS.AP_SUPPLIERS AP
    WHERE 
        MTL.INVENTORY_ITEM_ID = MSI.INVENTORY_ITEM_ID
    AND 
        AP.SEGMENT1 = MSI.VENDORCODE 
    AND 
        ORGANIZATION_ID = :STOREID 
    AND 
        MSI.DIVISIONID = NVL(:DIVISIONID,MSI.DIVISIONID)
    AND 
        MSI.BRANDID = NVL(:BRANDID,MSI.BRANDID)
    AND 
        MSI.BARCODE LIKE  '%' || :BARCODE || '%'
    AND 
        MSI.DESCRIPTION LIKE '%' || :DESCRIPTION || '%'
    AND 
        MSI.ITEMTYPE = '10'
    ";

    public static $SQL_REASON_INSERT = "
    
    INSERT IGNORE INTO `inv_schedule_reason`(`scheduleid`, `reason`, `username`, `userid`, `statusid`, `statusname`, `transactiondate`)
    VALUES (:SCHEDULEID,:REASON,:USERNAME,:USERID,:STATUSID,:STATUSNAME,:TRANSACTIONDATE)

    ";

    public static $SQL_BRANCH_GROUP_GET = "
    SELECT INB.groupid,SG.groupname from inv_batch INB
    inner join sys_group SG ON SG.groupid = INB.groupid 
    where storeid = :STOREID
    and INB.recordstatus in (1,2)
    group by groupid
    ORDER BY groupid desc

    ";
    public static $SQL_ITEM_CHECK = "
    SELECT productid FROM `inv_product`  INP
    inner join `inv_batch` INB ON INB.batchid = INP.batchid
    and INP.groups = :GROUPID AND INP.barcode = :BARCODE
    ";


    public static $SQL_ITEM_SEARCH = "
    select scheduleid,INPD.brandname,INPD.divisionname,storename,groupname,scheduledate,
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.divisionid IS NULL THEN 0 ELSE INPD.divisionid END AS divisionid,
    CASE WHEN INPD.brandid IS NULL THEN 0 ELSE INPD.brandid END AS brandid,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description
     FROM inv_schedule INS
    INNER JOIN inv_product INP on INP.productid = INS.productid
    LEFT JOIN inv_product_dw INPD on INPD.barcode = INP.barcode
    INNER JOIN inv_batch INB on INB.batchid = INP.batchid
    INNER JOIN sys_group SG on SG.groupid = INB.groupid
    WHERE `scheduledate` BETWEEN :DATEFROM AND :DATETO
    AND INS.statusid = 1
    AND INB.storeid = IFNULL(:STOREID,INB.storeid)
    AND INB.groupid = IFNULL(:GROUPID,INB.groupid)
    AND IFNULL(INPD.divisionid,0) = IFNULL(:DIVISIONID,INPD.divisionid)
    AND IFNULL(INPD.brandid,0) = IFNULL(:BRANDID,INPD.brandid)
    AND INP.barcode like  CONCAT('%', :BARCODE, '%') 
    AND INP.legacybarcode like  CONCAT('%', :LEGACYBARCODE, '%')
    AND INP.description like CONCAT('%', :DESCRIPTION, '%')
    
    UNION ALL

    select scheduleid,INPD.brandname,INPD.divisionname,storename,groupname,scheduledate,
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.divisionid IS NULL THEN 0 ELSE INPD.divisionid END AS divisionid,
    CASE WHEN INPD.brandid IS NULL THEN 0 ELSE INPD.brandid END AS brandid,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description
     FROM inv_schedule INS
    INNER JOIN inv_product INP on INP.productid = INS.productid
    LEFT JOIN inv_product_dw INPD on INPD.barcode = INP.barcode
    INNER JOIN inv_batch INB on INB.batchid = INP.batchid
    INNER JOIN sys_group SG on SG.groupid = INB.groupid
    WHERE `scheduledate` BETWEEN :DATEFROM AND :DATETO
    AND INS.statusid = 1
    AND INB.storeid = IFNULL(:STOREID,INB.storeid)
    AND INB.groupid = IFNULL(:GROUPID,INB.groupid)
    AND INPD.divisionid IS NULL
    AND INPD.brandid IS NULL
    AND INP.barcode like  CONCAT('%', :BARCODE, '%') 
    AND INP.legacybarcode like  CONCAT('%', :LEGACYBARCODE, '%')
    AND INP.description like CONCAT('%', :DESCRIPTION, '%')
    ;
    ";
  
    public static $SQL_ITEM_CHILD = "
    select *,
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description
    from inv_schedule INS
    inner join inv_product INP on INP.productid = INS.productid
    left join inv_product_dw INPD on INPD.barcode = INP.barcode
    inner join inv_batch INB on INB.batchid = INP.batchid
    inner join sys_group SG on SG.groupid = INB.groupid
    inner join inv_schedule_reason INSR on INSR.scheduleid = INS.scheduleid 
    WHERE INSR.statusid = :STATUSID 
    order by INS.scheduleid desc
    ;
    ";

    public static $ORA_LOAD_SUPPLIER = "SELECT DISTINCT VENDORCODE,VENDOR_NAME FROM XXCH_ALLSITE_PRODUCT_V WHERE ORGANIZATION_ID = :ORG_ID";
    public static $ORA_LOAD_CATEGORY = "SELECT DISTINCT CATEGORYID,CATEGORYNAME FROM XXCH_ALLSITE_PRODUCT_V WHERE ORGANIZATION_ID = :ORG_ID";
    public static $ORA_LOAD_PRODUCT = "SELECT * FROM XXCH_ALLSITE_PRODUCT_V WHERE ORGANIZATION_ID = 101";

    public static $SQL_ITEM_ADD = "INSERT IGNORE INTO inv_schedule (PRODUCTID,SCHEDULEDATE,BATCHID,STATUSID,REMARKS) VALUES(
    :PRODUCTID
    ,:SCHEDULEDATE,
     (SELECT INB.batchid FROM `inv_product`  INP
    inner join `inv_batch` INB ON INB.batchid = INP.batchid
    and INP.groups = :GROUPID and recordstatus = 2 and INB.storeid = :STOREID
    LIMIT 1
     )  ,:STATUSID,:REMARKS)";

    public static $SQL_ITEM_TRANSFER = "UPDATE inv_schedule SET STATUSID = :STATUSID WHERE SCHEDULEID = :SCHEDULEID";
    public static $SQL_INVENTORY_STATUS = "UPDATE inv_schedule SET STATUSID = :STATUSID WHERE SCHEDULEID = :SCHEDULEID";

    ////////////////////////////////////////////////////////////////////////////////////////
    // DASHBOARD MODULE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    public static $SQL_DASHBOARD_GET = "
    SELECT INS.scheduleid,INS.scheduledate,sellingarea,groupname,statusname,INP.productid,
    INB.storeid,INB.storename,INS.statusid,
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) AS onhand,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) AS sellout,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) AS receiving,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) AS adjustment,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) AS sellingarea,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) AS warehousearea,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) AS variance,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) AS adj_issue,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) AS adj_receipt,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) AS rcv_ir,
    TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) AS rcv_er,
    CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
    'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) END AS cic
    FROM inv_schedule INS
    INNER JOIN sys_status SS ON SS.StatusId = INS.statusid 
    INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
    INNER JOIN inv_product INP ON INP.productid = INS.productid
    INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
    LEFT JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode
    WHERE INS.statusid IN (1,2,6)  
    AND `scheduledate` BETWEEN :DATEFROM AND :DATETO
    AND INB.storeid = IFNULL(:STOREID,INB.STOREID)
    AND INB.groupid = IFNULL(:GROUPID,INB.GROUPID)
    AND INB.recordstatus != 3 
    ORDER BY groupname,INPD.divisionname,description asc
    ;
    ";

    public static $ORA_INVENTORY_GET = "
    SELECT 
     SCHEDULEDATE,SCHEDULEID,ONHAND_QTY,SELLING_QTY,RECEIVING_QTY,ADJUSTMENT_QTY,RESERVATION_QTY,ADJ_ISSUE,ADJ_RECEIPT,RCV_IR,RCV_ER
     FROM DWT_EX_PRODUCT_CIC_V2 
    WHERE 
        SCHEDULEDATE BETWEEN ( TO_DATE(SYSDATE , 'DD-MM-YY')) AND ( TO_DATE(SYSDATE , 'DD-MM-YY'))
    AND
        ORGANIZATION_ID = :STOREID
    ";

    public static $ORA_INVENTORY_GET_V2 = "
        SELECT 
            * 
        FROM 
            XXCH_PRODUCT_CIC_UPDATER2_V
        WHERE 
            BARCODE = :BARCODE
        AND 
            ORGANIZATION_ID = :STOREID
        AND 
            SCHEDULEDATE = TO_DATE(TO_DATE(:SCHEDULEDATE, 'YYYY-MM-DD'), 'dd-Mon-yy')
    ";

    public static $ORA_INVENTORY_GET_DASHBOARD = "
    SELECT 
     SCHEDULEDATE,SCHEDULEID,ONHAND_QTY,SELLING_QTY,RECEIVING_QTY,ADJUSTMENT_QTY,RESERVATION_QTY,ADJ_ISSUE,ADJ_RECEIPT,RCV_IR,RCV_ER
     FROM DWT_EX_PRODUCT_CIC_V2 
    WHERE 
        SCHEDULEDATE BETWEEN ( TO_DATE(SYSDATE , 'DD-MM-YY')) AND ( TO_DATE(SYSDATE , 'DD-MM-YY'))
    AND
        ORGANIZATION_ID = :STOREID 
    ";

    public static $SQL_DASHBOARD_LEDGER = "
    SELECT 
    INS.scheduledate,
    CASE WHEN onhand IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) END AS onhand,
    CASE WHEN sellout IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) END AS sellout,
    CASE WHEN receiving IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) END AS receiving,
    CASE WHEN adjustment IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) END AS adjustment,
    CASE WHEN sellingarea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) END AS sellingarea,
    CASE WHEN warehousearea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) END AS warehousearea,
    CASE WHEN variance IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) END AS variance,
    CASE WHEN adj_issue IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) END AS adj_issue,
    CASE WHEN adj_receipt IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) END AS adj_receipt,
    CASE WHEN rcv_ir IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) END AS rcv_ir,
    CASE WHEN rcv_er IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) END AS rcv_er,
    remarks
    FROM
        inv_schedule INS
    INNER JOIN sys_status SYS ON SYS.StatusId = INS.statusid 
    INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
    INNER JOIN inv_product INP ON INP.productid = INS.productid
    INNER JOIN sys_group SSG ON SSG.groupid  = INB.groupid
    LEFT JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode
    WHERE INS.statusid in (1,2,6) 
    AND INPD.barcode = :BARCODE		
    AND INB.storeid = :STOREID
    AND INB.recordstatus != 3
    ORDER BY 
        INS.scheduledate ASC
    ;
    ";

    public static $SQL_DASHBOARD_CURRENTCOUNT = "
    SELECT INS.scheduleid,INS.scheduledate,groupname,INP.productid,statusname,
    CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
    CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
    CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description,
    INB.storename,INB.storeid,
    CASE WHEN onhand IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) END AS onhand,
    CASE WHEN sellout IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) END AS sellout,
    CASE WHEN receiving IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) END AS receiving,
    CASE WHEN adjustment IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) END AS adjustment,
    CASE WHEN sellingarea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) END AS sellingarea,
    CASE WHEN warehousearea IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) END AS warehousearea,
    CASE WHEN variance IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) END AS variance,
    CASE WHEN adj_issue IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) END AS adj_issue,
    CASE WHEN adj_receipt IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) END AS adj_receipt,
    CASE WHEN rcv_ir IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) END AS rcv_ir,
    CASE WHEN rcv_er IS NULL THEN '' ELSE TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) END AS rcv_er,
    CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
    'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) END AS cic
    FROM inv_schedule INS
    INNER JOIN sys_status SS ON SS.StatusId = INS.statusid 
    INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
    INNER JOIN inv_product INP ON INP.productid = INS.productid
    INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
    INNER JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode
    WHERE INS.statusid IN (1,2,6)
    AND `scheduledate` BETWEEN :DATEFROM AND :DATETO AND INB.storeid = IFNULL(:STOREID,INB.STOREID)
    AND INB.groupid = IFNULL(:GROUPID,INB.GROUPID)
    AND INB.recordstatus != 3 
    
   ORDER BY groupname,INPD.divisionname,description ASC
    ";


    public static $SQL_DASHBOARD_CURRENTCOUNT_STORE = "
    SELECT INS.scheduleid,INP.barcode,INPD.description,INS.scheduledate,groupname,onhand,sellout,adjustment,receiving,statusname,INP.productid,
    CASE WHEN sellingarea = '0' THEN ' ' ELSE (sellingarea) END AS sellingarea,
    CASE WHEN warehousearea = '0' THEN ' ' ELSE (warehousearea) END AS warehousearea,
    CASE WHEN variance = '0' THEN ' ' ELSE (variance) END AS variance,
    INB.storename,INB.storeid,INS.adj_issue,INS.adj_receipt,INS.rcv_ir,INS.rcv_er,
    CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
    'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeidAND emp_id = IFNULL(null,emp_id)) END AS cic
    FROM inv_schedule INS
    INNER JOIN sys_status SS ON SS.StatusId = INS.statusid 
    INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
    INNER JOIN inv_product INP ON INP.productid = INS.productid
    INNER JOIN sys_group SG ON SG.groupid  = INB.groupid
    LEFT JOIN inv_product_dw INPD ON INPD.barcode = INP.barcode     
    WHERE INS.statusid IN (1,2,6) 
    AND `scheduledate` BETWEEN :DATEFROM AND :DATETO AND INB.storeid = IFNULL(:STOREID,INB.STOREID)
    ORDER BY groupname,INPD.divisionname,description ASC
    ";
    // ORDER BY cic,groupname,divisionname,description ASC
    // AND FIND_IN_SET(INPD.DIVISIONID, (SELECT DIVISIONID FROM sys_users_division where EMP_ID = :EMP_ID))
	public static $SQL_CATEGORY_GET = "SELECT * FROM sys_category WHERE RECORDSTATUS = 4";
    public static $SQL_BRANCH_GET = "SELECT * FROM sys_branch WHERE RECORDSTATUS = 4";
    public static $SQL_STATUS_GET = "SELECT * FROM sys_status WHERE IDENTIFIER = :IDENTIFIER";
    public static $SQL_LOG_INSERT = "
            INSERT IGNORE INTO rpt_db_update(STOREID,DESCRIPTION,LOGDATE,STATUS) VALUES(:STOREID,:DESCRIPTION,(SELECT CONCAT(CURDATE(),' ',TIME_FORMAT(CURRENT_TIME(), '%r'))),:STATUS)
            ON DUPLICATE KEY
            UPDATE
            STATUS = VALUES(STATUS),
            LOGDATE = VALUES(LOGDATE)
    ";

    ////////////////////////////////////////////////////////////////////////////////////////
    // INVENTORY MODULE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    public static $SQL_INVENTORY_GET = "
        SELECT 
        scheduleid,groupname,scheduledate,remarks,tlremarks,atlremarks,statusname,
        CASE WHEN INPD.barcode IS NULL THEN INP.barcode ELSE INPD.barcode END AS barcode,
        CASE WHEN INPD.legacybarcode IS NULL THEN INP.legacybarcode ELSE INPD.legacybarcode END AS legacybarcode,
        CASE WHEN INPD.description IS NULL THEN INP.description ELSE INPD.description END AS description,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM onhand))) AS onhand,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellout))) AS sellout,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM receiving))) AS receiving,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adjustment))) AS adjustment,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM sellingarea))) AS sellingarea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM warehousearea))) AS warehousearea,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM variance))) AS variance,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_issue))) AS adj_issue,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM adj_receipt))) AS adj_receipt,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_ir))) AS rcv_ir,
        TRIM(LEADING '0' FROM TRIM(TRAILING '.' FROM TRIM(TRAILING '00' FROM rcv_er))) AS rcv_er,
        CASE WHEN (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) IS NULL THEN 
        'ZZZZ' ELSE (SELECT GROUP_CONCAT(full_name SEPARATOR '/') FROM sys_users_division WHERE divisionid LIKE CONCAT('%', INPD.divisionid, '%') AND storeid = INB.storeid AND emp_id = IFNULL(null,emp_id)) END AS cic
        FROM inv_schedule INS
        INNER JOIN sys_status SS ON SS.STATUSID = INS.STATUSID 
        INNER JOIN inv_batch INB ON INB.BATCHID = INS.BATCHID
        INNER JOIN inv_product INP ON INP.PRODUCTID = INS.PRODUCTID
        INNER JOIN sys_group SG ON SG.GROUPID  = INB.GROUPID
        LEFT JOIN inv_product_dw INPD ON INPD.BARCODE = INP.BARCODE
        WHERE INS.STATUSID = :STATUSID
        AND INS.SCHEDULEDATE BETWEEN :DATEFROM AND :DATETO
        AND INB.STOREID = :STOREID
        AND INB.groupid = IFNULL(:GROUPID,INB.GROUPID)
        AND INB.RECORDSTATUS != 3
        ORDER BY groupname,INPD.divisionname,description ASC
    ";
    // ORDER BY cic,groupname,INPD.divisionname,description ASC

    public statIc $SQL_LOG_GET = "SELECT * FROM rpt_db_update";
    public static $SQL_USERS_DIVISION_GET = " SELECT emp_id,divisionid,userdivisionid,storeid,username,password,statusid FROM sys_users_division WHERE userdivisionid = :USERDIVISIONID";
    public static $SQL_INV_STORE_INPUT_GET = "
        SELECT 
            scheduleid,onhand,sellout,receiving,adjustment,reservation,
            VARIANCE,remarks,tlremarks,atlremarks,statusid,sellingarea,
            warehousearea,adj_issue,adj_receipt,rcv_ir,rcv_er
        FROM inv_schedule INS
        INNER JOIN inv_batch INB ON INB.batchid = INS.batchid
        WHERE statusid IN (1,2,6)  AND INB.recordstatus != 3
        AND INS.SCHEDULEDATE BETWEEN :DATEFROM AND :DATETO
    ";

    // AND isSent = 0

    public static $SQL_INV_STORE_INPUT_UPDATE = " UPDATE inv_schedule SET isSent = 1 WHERE isSent = 0 AND statusid = 6 ";
    public static $SQL_STORE_DB_CREDENTIALS_GET = "SELECT storeid,storename,dsn,username,password,lastupdate,serverip FROM sys_store_db WHERE recordstatus = 1 AND storeid = :STOREID";
    public static $ORA_DEPARTMENT_GET = "SELECT DISTINCT DEPARTMENTID,DEPARTMENTNAME FROM  DWT_DIM_PRODUCT";
    public static $ORA_DIVISION_GET = "
        SELECT 
            DISTINCT DIVISIONID,DIVISIONNAME 
        FROM
             XXCH_MTLCATEGORIES
        WHERE 
            ORGANIZATION_ID = 87
        ORDER BY 
            DIVISIONNAME
    
        ";
    public static $ORA_ROLE_GET = "SELECT ROLE_ID,ROLE_NAME FROM OSIPOS_ROLES";
    public static $ORA_SUPPLIER_GET = "SELECT SUPPLIERID,VENDOR_NAME FROM DWT_DIM_SUPPLIER";
    public static $ORA_CATEGORY_GET = "SELECT DISTINCT CATEGORYID,CATEGORYNAME FROM DWT_EX_PRODUCT_CAT";

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //    USER MODULE
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    public static $SQL_CHANGE_PASSWORD = "

                UPDATE sys_users_division 
                    SET PASSWORD = :PASSWORD,
                    DATE_UPDATED = :DATE_UPDATED,
                    USER_UPDATED = :USER_UPDATED
                    WHERE USERDIVISIONID = :USERDIVISIONID
                ";
    public static $SQL_CHECK_PASSWORD = "SELECT userdivisionid,emp_id FROM sys_users_division WHERE password = :PASSWORD and userdivisionid = :USERDIVISIONID";

    public static $SQL_AUTHENTICATE_LOGIN = "
    SELECT STORE_NAME,FULL_NAME,department,emp_id,rolename,department,userdivisionid  FROM sys_users_division
    WHERE USERNAME = :USERNAME AND password = :PASSWORD and storeid = -1 and statusid = 4";

    public static $ORA_USER_DEPARTMENT_GET = "
                                    SELECT
                                flex_value_id AS departmentid,
                                description AS departmentname
                            FROM
                                apps.fnd_flex_values_vl
                            WHERE
                                ( ( '' IS NULL )
                                  OR ( structured_hierarchy_level IN (
                                    SELECT
                                        hierarchy_id
                                    FROM
                                        apps.fnd_flex_hierarchies_vl h
                                    WHERE
                                        h.flex_value_set_id = 1016228
                                        AND h.hierarchy_name LIKE ''
                                ) ) )
                                AND ( flex_value_set_id = 1016228 )
                                AND flex_value_id in (60666,60669,60680)
                                    ";

    public static $SQL_USERS_GET = " SELECT emp_id as employee_number,full_name,rolename as job_description,store_name as assigned_store,storeid as store_id,department as departments,divisionname,job_code,userdivisionid,password,divisionid,username FROM `sys_users_division` WHERE storeid = :STOREID AND department = :DEPARTMENTS ";

    protected static $SQL_USER_UPDATE = "
                                UPDATE 
                                    SYS_USERS 
                                SET
                                    USERNAME = :USERNAME, USERPASSWORD = :USERPASSWORD,PERSONNELNAME = :PERSONNELNAME,DIVISIONID = :DIVISIONID ,
                                    ROLEID = :ROLEID,DEPARTMENTID = :DEPARTMENTID, RECORDSTATUS = :RECORDSTATUS,DEPARTMENTNAME = :DEPARTMENTNAME,
                                    DIVISIONNAME = :DIVISIONNAME, ROLENAME = :ROLENAME,USERPASSWORD = :USERPASSWORD
                                WHERE 
                                    USERID = :USERID
                            ";




    public static $SQL_USERS_DIVISION_INSERT = "
                    INSERT IGNORE INTO sys_users_division (emp_id,divisionid,divisionname,storeid,username,password,full_name,store_name,department,rolename,job_code,date_created,user_created)
                    VALUES(:emp_id,:divisionid,:divisionname,:storeid,:username,:password,:full_name,:store_name,:department,:rolename,:job_code,:date_created,:user_created)
                            ";

    public static $SQL_USERS_DIVISION_UPDATE = "
                    UPDATE 
                        sys_users_division
                    SET
                        username = :USERNAME,
                        password = :PASSWORD,
                        divisionid = :DIVISIONID,
                        divisionname = :DIVISIONNAME,
                        storeid = :STOREID,
                        store_name = :STORE_NAME,
                        statusid = :STATUSID,
                        date_updated = :DATE_UPDATED,
                        user_updated = :USER_UPDATED
                    WHERE
                        userdivisionid = :USERDIVISIONID
                    ";

    protected static $SQL_USERS_DIVISION_DELETE = "DELETE FROM sys_users_division WHERE USERDIVISIONID = :USERDIVISIONID "; 

	public static $ORA_POS_USERS_GET = "
    SELECT OU.USER_ID,OU.FIRST_NAME|| ' ' ||OU.LAST_NAME AS FULL_NAME,OU.EMPLOYEE_NUM AS EMPLOYEE_NUMBER,
    OUS.STORE_ID,OS.STORE_NAME AS ASSIGNED_STORE,OSR.ROLE_ID,OSR.ROLE_NAME AS Job_Description,
    0 AS DIVISION_ID, 'NOT SET' AS DIVISION_NAME,OU.PASSWORD,'NOT SET' AS DEPARTMENTS
    FROM OSIPOS_USER OU
    LEFT JOIN OSIPOS_USER_STORES OUS ON OUS.USER_ID = OU.USER_ID
    LEFT JOIN OSIPOS_STORE OS ON OS.STORE_ID = OUS.STORE_ID
    LEFT JOIN OSIPOS_USER_ROLES OUR ON OUR.USER_ID = OU.USER_ID
    LEFT JOIN OSIPOS_ROLES OSR ON OSR.ROLE_ID = OUR.ROLE_ID
    WHERE STATUS = 1 AND OS.STORE_NAME = :ASSIGNMENT
    GROUP BY OU.USER_ID,OU.FIRST_NAME,OU.LAST_NAME,OU.EMPLOYEE_NUM,OUS.STORE_ID,OSR.ROLE_ID,OSR.ROLE_NAME,OS.STORE_NAME,OU.PASSWORD
    ORDER BY EMPLOYEE_NUM
    ";
    

    ////////////////////////////////////////////////////////////////////////////////////////
    // GROUP MODULE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////

    //Check if there is approved item in under the group then update to posted
    public static $SQL_GROUP_STATUS_UPDATE = "
    update inv_batch INS
    set recordstatus = 2
    where (select count(*) as total from inv_schedule
    where statusid = 6 and batchid = INS.batchid) != 0
    ";

    public static $SQL_CHECK_HOLIDAY = "
        SELECT 
            * 
        FROM 
            sys_activedate
        WHERE
            datedesc = :DATEDESC
        AND
             recordstatus = 5
    ";

    ////////////////////////////////////////////////////////////////////////////////////////
    // ORACLE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////
            // LEFT JOIN OSIPOS_USER_ROLES UR ON UR.USER_ID = OSU.USER_ID

    public static $ORA_HO_USERS_GET = "
                WITH EMPLOYEES AS 
            (SELECT EMPLOYEE_NUMBER,c.FULL_NAME,:STOREID AS store_id,'NOT SET' AS divisionname,'0' AS userdivisionid,
                (SELECT a.deScription
             FROM FND_FLEX_VALUES_VL a
                WHERE (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,1))=a.FLEX_VALUE AND (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,1))=a.FLEX_VALUE 
                AND (('' IS NULL) OR (structured_hierarchy_level IN (SELECT hierarchy_id FROM fnd_flex_hierarchies_vl h WHERE h.flex_value_set_id = 1016227 AND h.hierarchy_name like ''))) and (FLEX_VALUE_SET_ID=1016227)) as DIVISION ,
                (SELECT a.deScription
             FROM FND_FLEX_VALUES_VL a
                WHERE (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,2))=a.FLEX_VALUE AND (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,2))=a.FLEX_VALUE 
                AND (('' IS NULL) OR (structured_hierarchy_level IN (SELECT hierarchy_id FROM fnd_flex_hierarchies_vl h WHERE h.flex_value_set_id = 1016228 AND h.hierarchy_name like ''))) and (FLEX_VALUE_SET_ID=1016228)) as DEPARTMENTS,   
            (SELECT a.deScription
             FROM FND_FLEX_VALUES_VL a
                WHERE (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,4))=a.FLEX_VALUE AND (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,4))=a.FLEX_VALUE 
                AND (('' IS NULL) OR (structured_hierarchy_level IN (SELECT hierarchy_id FROM fnd_flex_hierarchies_vl h WHERE h.flex_value_set_id = 1016230 AND h.hierarchy_name like ''))) and (FLEX_VALUE_SET_ID=1016230)) as Job_Description ,
              (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,4)) as job_code,
            (SELECT a.deScription  
              FROM FND_FLEX_VALUES_VL a
                WHERE (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,3))=a.FLEX_VALUE AND (REGEXP_SUBSTR(b.D_POSITION_ID,'[^.]+',1,3))=a.FLEX_VALUE 
                AND (('' IS NULL) OR (structured_hierarchy_level IN (SELECT hierarchy_id FROM fnd_flex_hierarchies_vl h WHERE h.flex_value_set_id = 1016229 AND h.hierarchy_name like ''))) and (FLEX_VALUE_SET_ID=1016229)) as Assigned_Store,
                        c.LAST_UPDATE_DATE, c.creation_date,c.D_TERMINATION_DATE as enddate,c.ATTRIBUTE1 as dtrid, B.D_POSITION_ID,C.PERSON_ID
              FROM PER_ASSIGNMENTS_V7 b ,PER_PEOPLE_V7 c
              WHERE c.PERSON_ID =b.PERSON_ID)
            SELECT assigned_store,departments,divisionname,employee_number,full_name,job_code,job_description,store_id,userdivisionid FROM EMPLOYEES 
            WHERE DEPARTMENTS = :DEPARTMENTS AND UPPER(Assigned_Store) = :ASSIGNMENT
            AND ENDDATE is null AND Job_Description != 'HR Staff'
            AND Job_Description NOT IN ('Counter Salesman',
            'Check-out Checker','Head Cashier','FIELD SALESMAN','Check-out Checker','In-House Merchandiser',
            'Paint Mixer','Merchandising Supervisor','SOG Stockman','SOG Utility','Terminal Cashier',
            'Warehouse In-Charge','Terminal Cashier (Reliever)','SOG Forklift Operator','Service Center Technician',
            'Alternate Head Cashier','SOG HR Staff','SOG Receiving In-Charge')   
    ";
 // LEFT JOIN OSIPOS_USER OSU ON OSU.EMPLOYEE_NUM = EMPLOYEE_NUMBER
    ////////////////////////////////////////////////////////////////////////////////////////
    // DATABASE FUNCTIONS
    ////////////////////////////////////////////////////////////////////////////////////////

     public function LOAD_ORA_DATA($sql,$parameter,$instance){
     	$options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_LOWER,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        ];


     	 switch($instance){
        	case 'dw':
				$myServer = 'xxxx';
				$myDB = 'xx';
				$oci_uname = 'xx';
				$oci_pass = 'xx';
        	break;
        	case 'xx':
				$myServer = 'xx';
				$myDB = 'xx';
				$oci_uname = 'xx';
				$oci_pass = 'xx';
        	break;
        }

        	$ob =  new stdClass();

            $tns = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".$myServer.")(PORT = 1521)))(CONNECT_DATA=(SID=".$myDB.")))";
            try {
                $conn = new PDO("oci:dbname=".$tns. ';charset=UTF8', $oci_uname, $oci_pass,$options);
                $sth = $conn->prepare($sql);
                $sth->execute($parameter);
                $datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
                return $datacontainer;
    
            } catch(PDOException $e) {
                // echo 'ERROR: ' . $e->getMessage();
                $ob->requeststatus = 'oracle loading data failed';
				$ob->errorinfo = $e->getMessage();
				$returndata = json_encode($ob);
				echo $returndata;
            }

     }


     public function LOAD_SQL_DATA($sql,$parameter){

		$datacontainer = array();
		$ob =  new stdClass();

		$db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
		$sth = $db->prepare($sql);

		if($sth->execute($parameter)){

			$datacontainer = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $datacontainer;

		}else{

			$ob->requeststatus = 'mysql loading data failed';
			$ob->errorinfo = $sth->errorInfo();
			$returndata = json_encode($ob);
            echo $returndata;
            
		}

     }

     public function MANAGE_SQL_DATA($sql,$parameter){
         
        $datacontainer = array();
        $ob =  new stdClass();

        $db = new PDO(AppSettings::LOAD_INI('SQLCON','dsn'),AppSettings::LOAD_INI('SQLCON','username'),AppSettings::LOAD_INI('SQLCON','password'));
        $sth = $db->prepare($sql);

        if($sth->execute($parameter)){

            $ob->requeststatus = 'successfully saved';
            $ob->status = 1;
            $ob->insertedId = $db->lastInsertId();

        }else{

            $ob->requeststatus = $sth->errorinfo();;
            $ob->status = 0;

        }

        return $ob;

     }

     public function EXPORT_EXCEL(){

        $data = $_POST['ledgerContent'];
        $filename="cic_ledger.xls";
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename='.$filename);
        $output = "
                    <table>
                     ".$data."
                    </table>";

        echo $output;
     }

}

// 60666  Information Technology
// 60669  Internal Audit Group 
// 60680  Store Operations Group

// EMPLOYEE_NUMBER,FULL_NAME,DEPARTMENTS,JOB_DESCRIPTION,JOB_CODE,ASSIGNED_STORE,DTRID

//      select distinct FLEX_VALUE_MEANING,DESCRIPTION from FND_FLEX_VALUES_VL]

// flex value meaning
// where description is not null
// and FLEX_VALUE_SET_ID = 1016229
// and flex_value_set_id = 1016229
// AND DESCRIPTION NOT LIKE ('%DO NOT USE%')
// AND FLEX_VALUE_MEANING NOT IN('11014','12000','11011','13023','12025','11025','22000','11000','14014','11026','14015','11020','15000','19000',
// '11012','11013','17000','11022','14023','11030','18000','11016','11031','14018','13019','16003','23000','14013','14000','11024',
// '17001','14026','12022','13020','21000','13018','13000','18001','11028','13022','17005','12017','14022','11033','11032','11029',
// '12019','12020','16005','16000','11019','13017','11027','11021','20000','14011','24000')

?>
