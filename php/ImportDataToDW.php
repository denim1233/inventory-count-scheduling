<?php
ini_set('max_execution_time', '1000');

IMPORT_DATA();

    function IMPORT_DATA(){
  
        $db = new PDO('mysql:host=192.168.4.57;port=3306;dbname=dw_dblink;charset=utf8','root','password');
        $sth = $db->prepare(
            "
                insert ignore into dw_dblink.CIC_INV_BATCH
                select * from dev_cicmonitoring.inv_batch as INB
                where INB.scheduletime > (select IFNULL(max(scheduletime),0) as scheduletime from dw_dblink.CIC_INV_BATCH);

                insert ignore into dw_dblink.CIC_INV_PRODUCT_DW
                select * from dev_cicmonitoring.inv_product_dw as INPD
                where INPD.datecreated > (select IFNULL(max(datecreated),0) as datecreated from dw_dblink.CIC_INV_PRODUCT_DW);

                insert ignore into dw_dblink.CIC_INV_PRODUCT
                select * from dev_cicmonitoring.inv_product as INP
                where INP.productid > (select IFNULL(max(productid),0) as productid from dw_dblink.CIC_INV_PRODUCT);

		        insert ignore into dw_dblink.CIC_INV_SCHEDULE
                select * from dev_cicmonitoring.inv_schedule as INS
                where INS.scheduleid > (select IFNULL(max(scheduleid),0) as scheduleid from dw_dblink.CIC_INV_SCHEDULE);

                insert ignore into dw_dblink.CIC_INV_BATCH
                select * from dev_cicmonitoring.inv_batch as INB
                where INB.scheduletime > (select IFNULL(max(scheduletime),0) as scheduletime from dw_dblink.CIC_INV_BATCH);

                insert ignore into dw_dblink.CIC_SYS_GROUP
                select * from dev_cicmonitoring.sys_group as SG
                where SG.groupid > (select IFNULL(max(groupid),0) as groupid from dw_dblink.CIC_SYS_GROUP);

		        insert ignore into dw_dblink.CIC_SYS_STATUS
                select * from dev_cicmonitoring.sys_status as SS
                where SS.statusid > (select IFNULL(max(statusid),0) as statusid from dw_dblink.CIC_SYS_STATUS);
 
            "
        );

        if($sth->execute()){
             echo "success import testing copy!";

        }else{
            print_r($sth->errorInfo());
        }
    }
?>                    