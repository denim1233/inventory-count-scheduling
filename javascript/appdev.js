var TBL_CONTAINER;
var COLUMN_CONTAINER = [];
var COLUMN_DEFS = [];
var REQUEST_CONTAINER;
var GROUP_PARAMETER = { action: 'load_group_settings' };
var HOLIDAY_PARAMETER = { action: 'load_holiday_settings' };
var TABLE_PARAMETER = [];
var module_name;
var rowdata;
var ROW_DATA_FOR_UPDATE;
var PAGE_NAME;
var BRANCH_ID;
var CATEGORY_NAME;
var REMARKS_ID;
var ctr = 0;

var SYS_GROUP_COLUMNS = [];
SYS_GROUP_COLUMNS[0] = { "data": "groupid" };
SYS_GROUP_COLUMNS[1] = { "data": "groupname" };
SYS_GROUP_COLUMNS[2] = { "data": "statusname" };
SYS_GROUP_COLUMNS[3] = { 
  targets: 3,
  data: null,
  render: function ( data, type, row ) {
    return  retdata = "<button type='button' id = 'btngroupdeactivate' class='btn btn-info'>Remove</button>";
  }
};


var GROUP_COLUMNDEFS = [];
GROUP_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
GROUP_COLUMNDEFS[1] = { "width": "50px", "targets": 3 };


var HOLIDAY_COLUMNS = [];
HOLIDAY_COLUMNS[0] = { "data": "holidayid" };
HOLIDAY_COLUMNS[1] = { "data": "holidayname" };
HOLIDAY_COLUMNS[2] = { "data": "holidaydate" };
HOLIDAY_COLUMNS[3] = { "data": "statusname" };
HOLIDAY_COLUMNS[4] = { 
  targets: 4,
  data: null,
  render: function ( data, type, row ) {
    return retdata = "<button type='button' id = 'btnholidaydeactivate' class='btn btn-info'>Remove</button>";
  }
};


var HOLIDAY_COLUMNDEFS = [];
HOLIDAY_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
HOLIDAY_COLUMNDEFS[1] = { "width": "50px", "targets": 4 };


// OU.USER_ID,OU.FIRST_NAME|| ' ' ||OU.LAST_NAME AS EMPLOYEE_NAME,OU.EMPLOYEE_NUM ,OU.PASSWORD,OU.STATUS,OUS.STORE_ID,OSR.ROLE_ID,OSR.ROLE_NAME,OS.STORE_NAME

var ASSIGNMENT_COLUMNS = [];
ASSIGNMENT_COLUMNS[0] = {"data": "employee_name"};
ASSIGNMENT_COLUMNS[1] = {"data": "employee_num"};
ASSIGNMENT_COLUMNS[2] = {"data": "role_name"};
ASSIGNMENT_COLUMNS[3] = {"data": "store_name"};
ASSIGNMENT_COLUMNS[4] = {"data": "division_name"};
ASSIGNMENT_COLUMNS[5] = {"data": "assignmentid"};
ASSIGNMENT_COLUMNS[6] = {"data": "store_id"};
ASSIGNMENT_COLUMNS[7] = {"data": "division_id"};
ASSIGNMENT_COLUMNS[8] = {"data": "user_id"};



var ASSIGNMENT_COLUMNDEFS = [];
ASSIGNMENT_COLUMNDEFS[0] = { "targets": [5], "visible": false, "searchable": false };
ASSIGNMENT_COLUMNDEFS[1] = { "targets": [6], "visible": false, "searchable": false };
ASSIGNMENT_COLUMNDEFS[2] = { "targets": [7], "visible": false, "searchable": true };
ASSIGNMENT_COLUMNDEFS[3] = { "targets": [8], "visible": false, "searchable": true };


// hide 5,6,7 columns


var INVENTORY_COLUMNS = [];
INVENTORY_COLUMNS[0] = { "data": "scheduleid" };
INVENTORY_COLUMNS[1] = { "data": "barcode" };
INVENTORY_COLUMNS[2] = { "data": "description" };
INVENTORY_COLUMNS[3] = { "data": "scheduledate" };
INVENTORY_COLUMNS[4] = { "data": "groupname" };
INVENTORY_COLUMNS[5] = { "data": "onhand" };
INVENTORY_COLUMNS[6] = { "data": "sellout" };
INVENTORY_COLUMNS[7] = { "data": "receiving" };
INVENTORY_COLUMNS[8] = { "data": "adjustment" };
INVENTORY_COLUMNS[9] = { "data": "sellingarea" };
INVENTORY_COLUMNS[10] = { "data": "warehousearea" };
INVENTORY_COLUMNS[11] = { "data": "variance" };
INVENTORY_COLUMNS[12] = { "data": "statusname" };
INVENTORY_COLUMNS[13] = { "data": "remarks2" };

var INVENTORY_COLUMNDEFS = [];
INVENTORY_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
INVENTORY_COLUMNDEFS[1] = { "width": "50px", "targets": 1 };
INVENTORY_COLUMNDEFS[2] = { "width": "200px", "targets": 2 };
INVENTORY_COLUMNDEFS[3] = { "width": "100px", "targets": 3 };
INVENTORY_COLUMNDEFS[4] = { "width": "130px", "targets": 4 };
INVENTORY_COLUMNDEFS[5] = { "width": "50px", "targets": 5 };
INVENTORY_COLUMNDEFS[6] = { "width": "50px", "targets": 6 };
INVENTORY_COLUMNDEFS[7] = { "width": "50px", "targets": 7 };
INVENTORY_COLUMNDEFS[8] = { "width": "50px", "targets": 8 };
INVENTORY_COLUMNDEFS[9] = { "width": "50px", "targets": 9 };
INVENTORY_COLUMNDEFS[10] = { "width": "50px", "targets": 10 };
INVENTORY_COLUMNDEFS[11] = { "width": "50px", "targets": 11 };
INVENTORY_COLUMNDEFS[12] = { "width": "100px", "targets": 13 };
INVENTORY_COLUMNDEFS[13] = { targets: 5, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[14] = { targets: 6, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[15] = { targets: 7, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[16] = { targets: 8, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[17] = { targets: 9, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[18] = { targets: 10, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[19] = { targets: 11, className: 'dt-body-right' };

var ITEM_COLUMNS = [];
ITEM_COLUMNS[0] = { "data": "productid" };
ITEM_COLUMNS[1] = { "data": "barcode" };
ITEM_COLUMNS[2] = { "data": "description" };
ITEM_COLUMNS[3] = { "data": "suppliername" };
ITEM_COLUMNS[4] = { "data": "groupname" };
ITEM_COLUMNS[5] = { "data": "categoryname" };
ITEM_COLUMNS[6] = { "data": "pcount" };
ITEM_COLUMNS[7] = { 
  targets: 6,
  data: null,
  render: function ( data, type, row ) {
    var retdata;
    return retdata = "<button type='button' id = 'btnmdschedule' class='btn btn-info'>Add</button>";
  }
};

var ITEM_CANCEL_COLUMNS = [];
ITEM_CANCEL_COLUMNS[0] = { "data": "scheduleid" };
ITEM_CANCEL_COLUMNS[1] = { "data": null };
ITEM_CANCEL_COLUMNS[2] = { "data": "barcode" };
ITEM_CANCEL_COLUMNS[3] = { "data": "description" };
ITEM_CANCEL_COLUMNS[4] = { "data": "scheduledate" };
ITEM_CANCEL_COLUMNS[5] = { 
  targets: 5,
  data: null,
  render: function ( data, type, row ) {
        return retdata = "<button type='button' id = 'btnrowcancel' class='btn btn-info'>Cancel</button>";
  }
};

var ITEM_CANCEL_COLUMNDEFS = [];
ITEM_CANCEL_COLUMNDEFS[0] = { 
  targets:[1],
  defaultContent:'',
  // data:null,
  type: "html",
   orderable: false,
  className:'',
  checkboxes:{
     selectRow:true
 },
 
  };
ITEM_CANCEL_COLUMNDEFS[1] = { "targets": [0], "visible": false, "searchable": false };
ITEM_CANCEL_COLUMNDEFS[2] = { "width": "40%", "targets": 3 };
ITEM_CANCEL_COLUMNDEFS[3] = { "width": "10%", "targets": 5 };

var ITEM_COLUMNDEFS = [];
ITEM_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
ITEM_COLUMNDEFS[1] = { targets: 6, className: 'dt-body-right' };
ITEM_COLUMNDEFS[2] = { "width": "40px", "targets": 7 };
ITEM_COLUMNDEFS[3] = { "width": "50px", "targets": 6 };

var INDEX_COLUMNS = [];
INDEX_COLUMNS[0] = { "data": "scheduleid" };
INDEX_COLUMNS[1] = { "data": "barcode" };
INDEX_COLUMNS[2] = { "data": "description" };
INDEX_COLUMNS[3] = { "data": "scheduledate" };
INDEX_COLUMNS[4] = { "data": "storename" };
INDEX_COLUMNS[5] = { "data": "groupname" };
INDEX_COLUMNS[6] = { "data": "onhand" };
INDEX_COLUMNS[7] = { "data": "sellout" };
INDEX_COLUMNS[8] = { "data": "receiving" };
INDEX_COLUMNS[9] = { "data": "adjustment" };
INDEX_COLUMNS[10] = { "data": "sellingarea" };
INDEX_COLUMNS[11] = { "data": "warehousearea" };
INDEX_COLUMNS[12] = { "data": "variance" };
INDEX_COLUMNS[13] = { "data": "statusname" };

INDEX_COLUMNS[14] = { 
  targets: 13,
  data: null,
  render: function ( data, type, row ) {
        return retdata = "<button type='button' id = 'btnledger' class='btn btn-info'>Ledger</button>";
  }
};

INDEX_COLUMNS[15] = { "data": "storeid" };


var INDEX_COLUMNDEFS = [];
INDEX_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
INDEX_COLUMNDEFS[1] = { "width": "30px", "targets": 1 };
INDEX_COLUMNDEFS[2] = { "width": "200px", "targets": 2 };
INDEX_COLUMNDEFS[3] = { "width": "120px", "targets": 3 };
INDEX_COLUMNDEFS[4] = { "width": "150px", "targets": 4 };
INDEX_COLUMNDEFS[5] = { "width": "50px", "targets": 5 };
INDEX_COLUMNDEFS[6] = { "width": "50px", "targets": 6 };
INDEX_COLUMNDEFS[7] = { "width": "50px", "targets": 7 };
INDEX_COLUMNDEFS[8] = { "width": "50px", "targets": 8 };
INDEX_COLUMNDEFS[9] = { "width": "50px", "targets": 9 };
INDEX_COLUMNDEFS[10] = { "width": "50px", "targets": 10 };
INDEX_COLUMNDEFS[11] = { "width": "50px", "targets": 11 };
INDEX_COLUMNDEFS[12] = { targets: 12, className: 'dt-body-right' };
INDEX_COLUMNDEFS[13] = { targets: 6, className: 'dt-body-right' };
INDEX_COLUMNDEFS[14] = { targets: 7, className: 'dt-body-right' };
INDEX_COLUMNDEFS[15] = { targets: 8, className: 'dt-body-right' };
INDEX_COLUMNDEFS[16] = { targets: 9, className: 'dt-body-right' };
INDEX_COLUMNDEFS[17] = { targets: 10, className: 'dt-body-right' };
INDEX_COLUMNDEFS[18] = { targets: 11, className: 'dt-body-right' };
INDEX_COLUMNDEFS[19] = { "targets": [15], "visible": false, "searchable": true };


var LEDGER_COLUMNS = [];
LEDGER_COLUMNS[0] = { "data": null };
LEDGER_COLUMNS[1] = { "data": "scheduledate" };
LEDGER_COLUMNS[2] = { "data": "onhand" };
LEDGER_COLUMNS[3] = { "data": "sellout" };
LEDGER_COLUMNS[4] = { "data": "receipts" };
LEDGER_COLUMNS[5] = { "data": "adjustment" };
LEDGER_COLUMNS[6] = { "data": "warehousearea" };
LEDGER_COLUMNS[7] = { "data": "sellingarea" };
LEDGER_COLUMNS[8] = { "data": "reservation" };
LEDGER_COLUMNS[9] = { "data": "variance" };
LEDGER_COLUMNS[10] = { "data": "remarks" };
LEDGER_COLUMNS[11] = { "data": "remarks" };

var LEDGER_COLUMNDEFS = [];
LEDGER_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
LEDGER_COLUMNDEFS[1] = { "width": "120px", "targets": 1 };
LEDGER_COLUMNDEFS[2] = { "width": "50px", "targets": 2 };
LEDGER_COLUMNDEFS[3] = { "width": "50px", "targets": 3 };
LEDGER_COLUMNDEFS[4] = { "width": "50px", "targets": 4 };
LEDGER_COLUMNDEFS[5] = { "width": "50px", "targets": 5 };
LEDGER_COLUMNDEFS[6] = { "width": "50px", "targets": 6 };
LEDGER_COLUMNDEFS[7] = { "width": "50px", "targets": 7 };
LEDGER_COLUMNDEFS[8] = { "width": "50px", "targets": 8 };
LEDGER_COLUMNDEFS[9] = { "width": "50px", "targets": 9 };
LEDGER_COLUMNDEFS[10] = { "width": "180px", "targets": 10 };
LEDGER_COLUMNDEFS[11] = { "width": "180px", "targets": 11 };
LEDGER_COLUMNDEFS[12] = { targets: 1, className: 'dt-body-right' };
LEDGER_COLUMNDEFS[13] = { targets: 2, className: 'dt-body-right' };
LEDGER_COLUMNDEFS[14] = { targets: 3, className: 'dt-body-right' };
LEDGER_COLUMNDEFS[15] = { targets: 4, className: 'dt-body-right' };
LEDGER_COLUMNDEFS[16] = { targets: 5, className: 'dt-body-right' };
LEDGER_COLUMNDEFS[17] = { targets: 6, className: 'dt-body-right' };

var USER_COLUMNS = [];
USER_COLUMNS[0] = { "data": "userid" };
USER_COLUMNS[1] = { "data": "username" };
// USER_COLUMNS[2] = { "data": "userpassword" };
USER_COLUMNS[2] = { "data": "rolename" };
USER_COLUMNS[3] = { "data": "categoryname" };
USER_COLUMNS[4] = { "data": "personnelname" };
USER_COLUMNS[5] = { "data": "branchname" };
USER_COLUMNS[6] = { "data": "statusname" };
USER_COLUMNS[7] = { 
  targets: 7,
  data: null,
  render: function ( data, type, row ) {
    var retdata;

    return retdata = "<button type='button' id = 'btnuseredit' class='btn btn-info'>Edit</button>";

  }
};

var USER_COLUMNDEFS = [];
USER_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };

$(document).ready(function () {


  $(document).on("keyup", "input", function (e) {
    if (e.which == 13) {
      if(this.id === 'txtusername' || this.id === 'txtpassword'){
        SAVE_DATA(PAGE_NAME,{username: $('#txtusername').val(),password: $('#txtpassword').val()},'user_authenticate');
      }
    }
  });

  $("#sidebar").load("sidebar.html");
  $("#appnavbar").load("navbar.html");
  $("#modalcontainer1").load("sys_group.html");
  $("#modalcontainer2").load("sys_holiday.html");

  
  PAGE_NAME = GET_PAGENAME();

      if(PAGE_NAME != 'login'){
        document.getElementById("loader").style.display = "none";
        USER_CONTROL('user_check',LOAD_NAV_DATA);
        // console.log(BRANCH_ID);
    }

//show future date selection only

if(PAGE_NAME === 'group'){
  var dateToday = GET_DATE_NOW();
document.getElementById("txtgrpdate").min = dateToday; 
document.getElementById("txtgrpdate").value = dateToday;
}

 



  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //  BUTTON CLICK EVENT
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // $.when(USER_CONTROL('user_check')).done(function(a1){


  $(document).on("click", "#btngenerateschedule", function () {
    REQUEST_CONTAINER = {action: 'rpt_schedule',datefrom: $('#dtfrom').val() , dateto:$('#dtto').val(),groupid:$('#cmbgroup').val(),storeid:$('#cmbbranch').val()};
    console.log(REQUEST_CONTAINER);
    COLUMN_CONTAINER = INVENTORY_COLUMNS;
    COLUMN_DEFS = INVENTORY_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
  });

  $(document).on("click", "#btnprint", function () {
        $(".buttons-print")[0].click();
  });
   $(document).on("click", "#btnexport", function () {
        $(".buttons-excel")[0].click();
  });


  $( "#btnsearch" ).click(function() {
    REQUEST_CONTAINER = { action: 'item_search', statusid: 1,datefrom: $('#dtfrom').val(),dateto: $('#dtto').val() };
    COLUMN_CONTAINER = ITEM_CANCEL_COLUMNS;
    COLUMN_DEFS = ITEM_CANCEL_COLUMNDEFS;
    LOAD_DATATABLE('#tbitemcancel');
  });

  $(document).on("click", "#btnsave", function () {

    SAVE_DATA(module_name, TABLE_PARAMETER, 'manage_settings');
    if(module_name === 'group'){
     COLUMN_CONTAINER = SYS_GROUP_COLUMNS;
     COLUMN_DEFS = GROUP_COLUMNDEFS;
     REQUEST_CONTAINER = GROUP_PARAMETER;
     LOAD_DATATABLE("#group_table");

   }else if (module_name === 'holiday'){

    COLUMN_CONTAINER = HOLIDAY_COLUMNS;
    COLUMN_DEFS = HOLIDAY_COLUMNDEFS;
    REQUEST_CONTAINER = HOLIDAY_PARAMETER;
    LOAD_DATATABLE("#holiday_table");

  }
});

  //check the parameter it should be the whole table
  $(document).on("click", "#btnpost", function () {

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Save'
      }).then((result) => {
      if (result.value) {
        SAVE_DATA(PAGE_NAME, TABLE_PARAMETER, 'inventory_post');
        }
      })
  });

  $(document).on("click", "#btnapprove", function () {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Save'
      }).then((result) => {
      if (result.value) {
        SAVE_DATA(PAGE_NAME, TABLE_PARAMETER, 'inventory_approve');
        }
      })

  });

  $(document).on("click", "#btnadd", function () {
    // console.log('add row!');
    ADD_NEW_ROW();
  });

  $(document).on("click", "#btnrowcancel", function () {
    SAVE_DATA(PAGE_NAME,{scheduleid:rowdata.scheduleid},'item_cancel_row');
  });


  $(document).on("click", "#btncancel", function () {

    var data = TBL_CONTAINER.rows( ['.even.selected', '.odd.selected'] ).data().toArray();
    SAVE_DATA(PAGE_NAME, data, 'item_cancel');

  });

  $(document).on("click", "#btnuseredit", function () {
    $('#txtuserid').val(rowdata.userid); 
    $('#txtusername').val(rowdata.username); 
    // $('#txtpassword').val(rowdata.userpassword); 
    $('#txtpersonnel').val(rowdata.personnelname); 
    $('#cmbrole').val(rowdata.roleid); 
    $('#cmbcategory').val(rowdata.categoryid);
    $('#cmbstatus').val(rowdata.recordstatus); 
    $('#cmbbranch').val(rowdata.branchid); 
    $('#UserModal').modal('show');
  });

  $(document).on("click", "#btnuseradd", function () {
    $('#txtuserid').val(0); 
    $('#txtusername').val(''); 
    $('#txtpassword').val(''); 
    $('#txtpersonnel').val(''); 
    $('#cmbrole').val(0); 
    $('#cmbcategory').val(0); 
    $('#cmbstatus').val(0); 
    $('#cmbbranch').val(0); 
    $('#UserModal').modal('show');
  });

  $(document).on("click", "#btnusersave", function () {
    parameter = {userid: $('#txtuserid').val() , username:$('#txtusername').val(),password: $('#txtpassword').val(),personnel: $('#txtpersonnel').val(),roleid:$('#cmbrole').val(),categoryid:$('#cmbcategory').val(),recordstatus:$('#cmbstatus').val(),branchid:$('#cmbbranch').val(),branchname:$("#cmbbranch option:selected").html(),categoryname:$("#cmbcategory option:selected").html()};
    SAVE_DATA(PAGE_NAME,parameter,'user_managerecord');
    // console.log(parameter);
    console.log('save the users!');
  });

  $(document).on("click", "button", function () {


    switch(this.id) {
      case 'btnmdschedule':
        //clear the values in the modal
        var datetoday  = GET_DATE_NOW();
        $('#txtdate').val(datetoday); 
        $('#lbdescription').html(rowdata.description);
        $('#lbbarcode').html(rowdata.barcode); 
        $('#txtbatch').val(rowdata.batchid); 
        $('#txtproductid').val(rowdata.productid); 

        $('#myModal').modal('show');
        break;

      case 'btngroupdeactivate':

        UPDATE =
        {
          "groupid": rowdata.groupid,
          "groupname": rowdata.groupname,
          "statusname": "Inactive",
          "recordstatus": 5
        };

        TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
        TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();
        SAVE_DATA('group', TABLE_PARAMETER, 'manage_record_settings');
        TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).remove().draw();
        break;
      
      case 'btnholidaydeactivate':

          UPDATE =
          {
            "holidayid": rowdata.holidayid,
            "holidayname": rowdata.holidayname,
            "holidaydate": rowdata.holidaydate,
            "statusname": "Inactive",
            "recordstatus": 5
          };
          
        TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
        TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();
        SAVE_DATA('holiday', TABLE_PARAMETER, 'manage_record_settings');
        TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).remove().draw();
        break;
  
      case 'btncreateschedule':
        var parameter = {scheduledate:$('#txtdate').val(),productid:$('#txtproductid').val(),groupid:$('#cmbgroup').children("option:selected").val(),batchid: $('#txtbatch').val()};
        SAVE_DATA(module_name, parameter , 'item_add_schedule');
        break;
      
      case 'btnledger':
        $('#lbdescription').html(rowdata.description); 
        $('#lbbarcode').html(rowdata.barcode);
        REQUEST_CONTAINER = { action: 'index_ledger', productid: rowdata.productid};
        LOAD_LEDGER();
        $('li > a[href="#menu1"]').tab("show");
        break;
      default:
        // code block
    }

  });


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                        LINK CLICK
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $(document).on("click", "a", function (event) {

    var divid = '#' + $(this).data("child");
    switch (this.id) {
      case 'nvlogout':
        USER_CONTROL('user_logout');
        return;
      // case 'lnkimportdata':
      //   event.preventDefault();
      //   Swal.fire({
      //     title: 'Are you sure?',
      //     text: "You won't be able to revert this!",
      //     icon: 'warning',
      //     showCancelButton: true,
      //     confirmButtonColor: '#3085d6',
      //     cancelButtonColor: '#d33',
      //     confirmButtonText: 'Save'
      //     }).then((result) => {
      //     if (result.value) {
      //       SAVE_DATA('' , '', 'importdata');
      //       }
      //     })

      //   return;
      case 'lnksysgroup':
        COLUMN_CONTAINER = SYS_GROUP_COLUMNS;
        COLUMN_DEFS = GROUP_COLUMNDEFS;
        REQUEST_CONTAINER = GROUP_PARAMETER;
        
        LOAD_DATATABLE("#group_table");
        module_name = 'group';
        $('#GroupModal').modal('show');
        return;
      case 'lnksysholiday':
        COLUMN_CONTAINER = HOLIDAY_COLUMNS;
        COLUMN_DEFS = HOLIDAY_COLUMNDEFS;
        REQUEST_CONTAINER = HOLIDAY_PARAMETER;
        LOAD_DATATABLE("#holiday_table");
        module_name = 'holiday';
        $('#HolidayModal').modal('show');
        return;
      case 'lnksettings':
        var x = document.getElementById('chldsettings');
        if (x.style.display === "none") {
          x.style.display = "block";
        } else {
          x.style.display = "none";
        }
      break;
      case 'lnkreport':
        module_name = 'productschedule';
        var x = document.getElementById('chldreport');
        if (x.style.display === "none") {
          x.style.display = "block";
        } else {
          x.style.display = "none";
        }
      break;
      default:
    }

            switch (this.id) {
          case 'main':
            REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6,categoryname:CATEGORY_NAME };
            COLUMN_DEFS = INDEX_COLUMNDEFS;

        COLUMN_CONTAINER = INDEX_COLUMNS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);
            break;
          case 'ledger':
            // REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6 };
            break;
          case 'currentcount':

            REQUEST_CONTAINER = { action: 'index_currentcount', statusid: 2 ,categoryname:CATEGORY_NAME};
            
        COLUMN_CONTAINER = INDEX_COLUMNS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);
            break;
        }



    switch(PAGE_NAME) {




      case 'index':
        if(this.id === 'lnkindex'){
          REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6,categoryname:CATEGORY_NAME };
          COLUMN_CONTAINER = INDEX_COLUMNS;
          LOAD_DATATABLE('#tb' + PAGE_NAME);
      }
        break;
      case 'inventory':
        //removed placed into access rights
        break;
      case 'group':
       
        break;
      case 'item':

        switch (this.id) {
          case 'lnkitemcancel':
            REQUEST_CONTAINER = { action: 'item_search', statusid: 1,datefrom: $('#dtfrom').val(),dateto: $('#dtto').val() };
            COLUMN_CONTAINER = ITEM_CANCEL_COLUMNS;
            COLUMN_DEFS = ITEM_CANCEL_COLUMNDEFS;
            LOAD_DATATABLE('#tbitemcancel');
            break;
        }
      
      case 'user':
 

        break;
      default:
        // code block
    }
    
  
  });


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //                        PAGE LOAD
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  if ($('#tb' + PAGE_NAME).length != 0) {

    //add parameter here in the request container for the status of the schedule
    switch(PAGE_NAME) {
      case 'index':
        REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6,categoryname:CATEGORY_NAME};
        COLUMN_CONTAINER = INDEX_COLUMNS;
        COLUMN_DEFS = INDEX_COLUMNDEFS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);

        LOAD_DATA_IN_COMBOBOX('cmbbranch', { action: "group_branch", module_name: 'group' }, 'php/AdminController.php', 'organization_id', 'organization_name',true);
   
        break;
      case 'inventory':

      setTimeout(function(){ 
        if($('#txtroleid').val() === '4' ){
          REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6,branchid: null};
        }else if ($('#txtroleid').val() === '1' || $('#txtroleid').val() === '2'){
          REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 2,branchid: BRANCH_ID};
        }else if ($('#txtroleid').val() === '3'){
          REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 1,branchid: BRANCH_ID};
        }

        COLUMN_CONTAINER = INVENTORY_COLUMNS;
        COLUMN_DEFS = INVENTORY_COLUMNDEFS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);
      },500);

        break;
      case 'group':
        // REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 1 };
        // COLUMN_CONTAINER = ITEM_COLUMNS;
        break;
      case 'item':
       
        REQUEST_CONTAINER = { action: PAGE_NAME};
        COLUMN_CONTAINER = ITEM_COLUMNS;
        COLUMN_DEFS = ITEM_COLUMNDEFS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);
        LOAD_DATA_IN_COMBOBOX('cmbcategory',{ action: "item_category", module_name: 'item',branchid: BRANCH_ID },"php/AdminController.php",'categoryname','categoryname',true)
        LOAD_DATA_IN_COMBOBOX('cmbsupplier',{ action: "item_supplier", module_name: 'item',branchid: BRANCH_ID },"php/AdminController.php",'vendor_name','vendor_name',true)
        break;
      case 'user':
        REQUEST_CONTAINER = { action: PAGE_NAME};
        COLUMN_CONTAINER = USER_COLUMNS;
        COLUMN_DEFS = USER_COLUMNDEFS;

        LOAD_DATA_IN_COMBOBOX('cmbrole',{ action: "user_role", module_name: 'user' },"php/AdminController.php",'roleid','rolename')
        LOAD_DATA_IN_COMBOBOX('cmbcategory',{ action: "user_category", module_name: 'user' },"php/AdminController.php",'categoryid','categoryname')
        LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user' },"php/AdminController.php",'organization_id','organization_name')
      
        $("#usermodalcontainer").load("sys_user.html");
        LOAD_DATATABLE('#tb' + PAGE_NAME);
        break;
      case 'assignment':
        REQUEST_CONTAINER = { action: PAGE_NAME};
        COLUMN_CONTAINER = ASSIGNMENT_COLUMNS;
        COLUMN_DEFS = ASSIGNMENT_COLUMNDEFS;
        LOAD_DATATABLE('#tb' + PAGE_NAME);
        LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user' },"php/AdminController.php",'organization_id','organization_name')
        LOAD_DATA_IN_COMBOBOX('fltdivision', { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname',true);
        LOAD_DATA_IN_COMBOBOX('fltrole', { action: "role", module_name: PAGE_NAME }, 'php/AdminController.php', 'role_id', 'role_name',true);

        break; 
      default:
        // code block
    }

}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            LOAD DATATABLE
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  function LOAD_DATATABLE(TABLE_NAME) {

    var $table = $(TABLE_NAME).dataTable({
    "processing": true,
    "serverSide": true,
    "bDestroy": true,
    "bJQueryUI": true,
    "ajax": {
        'type': 'POST',
        'url': 'php/AdminController.php',
        'data': REQUEST_CONTAINER,
        'dataType': "JSON",
    },
    "columns": COLUMN_CONTAINER,
    "columnDefs": COLUMN_DEFS,
     select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            dom: '<"row"<"col-sm-6"Bl><"col-sm-6"f>>' +
        '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
        '<"row"<"col-sm-5"i><"col-sm-7"p>>',
      fixedHeader: {
        header: true
      },
      buttons: {
        buttons: [{

          extend: 'print',
          text: '<i class="fa fa-print"></i> Print',
          title: $('h1').text(),
          exportOptions: {
                columns: [  1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12 ]

          },
           className: "buttonsToHide",
          footer: true,
          autoPrint: false
        }, 

        {
          extend: 'excel',
          text: '<i class="fa fa-file-pdf-o"></i> Excel',
          title: $('h1').text(),
          exportOptions: {
              columns: [  1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12 ]
          },
            className: "buttonsToHide",
          footer: true
        }],

        dom: {
          container: {
            className: 'dt-buttons'
          },
          button: {
            className: 'btn btn-default'
          }
        }
      },
  });

    return;


    TBL_CONTAINER = null;
    $(TABLE_NAME).dataTable().fnClearTable();
    $(TABLE_NAME).dataTable().fnDestroy();
    $.ajax({
      url: "php/AdminController.php",
      method: "POST",
      data: REQUEST_CONTAINER,
      dataType: "JSON",
      beforeSend: function() {
        LoadingControl();
      },
      complete: function() {
        LoadingControl();
      },
      success: function (data) {

        if ($.fn.DataTable.isDataTable(TABLE_NAME) === false) {
          TBL_CONTAINER = $(TABLE_NAME).DataTable({
            "autoWidth": false,
            "data": data,
            "columns": COLUMN_CONTAINER,
            "columnDefs": COLUMN_DEFS,
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            dom: '<"row"<"col-sm-6"Bl><"col-sm-6"f>>' +
        '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
        '<"row"<"col-sm-5"i><"col-sm-7"p>>',
      fixedHeader: {
        header: true
      },
      buttons: {
        buttons: [{

          extend: 'print',
          text: '<i class="fa fa-print"></i> Print',
          title: $('h1').text(),
          exportOptions: {
                columns: [  1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12 ]

          },
           className: "buttonsToHide",
          footer: true,
          autoPrint: false
        }, 

        {
          extend: 'excel',
          text: '<i class="fa fa-file-pdf-o"></i> Excel',
          title: $('h1').text(),
          exportOptions: {
              columns: [  1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12 ]
          },
            className: "buttonsToHide",
          footer: true
        }],

        dom: {
          container: {
            className: 'dt-buttons'
          },
          button: {
            className: 'btn btn-default'
          }
        }
      },
            order: [2, 'asc'],
            "deferRender": true
          });
        } else {
          if (data) {
            TBL_CONTAINER.clear();
            TBL_CONTAINER.rows.add(data).draw();
          }
        }
  
        var celldata;
        var cell;
        var originaldata;
        var cellindex = 0;

        // $(TABLE_NAME + ' tbody').on('click', 'input[type="checkbox"]', function () {
        //   // alert('kainis!');
        //    var row = $(this).parent().parent();

        //    if ($(this).is(":checked"))
        //    {
        //      $(row).removeClass("selected");
        //      $(row).toggleClass('selected');
        //    }
        //    else{
        //      $(row).removeClass("selected");

        //    }


        //    // console.log(row);

        // });

        //  TBL_CONTAINER.on("click", 'input[type="checkbox"]', function() {
        //       if ($(this).is(":checked"))
        //    {
        //       TBL_CONTAINER.rows().select();
        //        var data = TBL_CONTAINER.rows( ['.even.selected', '.odd.selected'] ).data().toArray();
        //        console.log(data);
        //    }else{
        //     TBL_CONTAINER.rows().deselect();
        //    }
        

      //     if ($("th.select-checkbox").hasClass("selected")) {
      //       TBL_CONTAINER.rows().deselect();
      //         $("th.select-checkbox").removeClass("selected");
      //     } else {
      //       TBL_CONTAINER.rows().select();
      //         $("th.select-checkbox").addClass("selected");
      //     }
      // }).on("select deselect", function() {
      //     ("Some selection or deselection going on")
      //     if (TBL_CONTAINER.rows({
      //             selected: true
      //         }).count() !== TBL_CONTAINER.rows().count()) {
      //         $("th.select-checkbox").removeClass("selected");
      //     } else {
      //         $("th.select-checkbox").addClass("selected");
      //     }
        // });


      //   TBL_CONTAINER.on("click", "th.select-checkbox", function() {

      //     alert('ambot!');
         
      // });

      ///ENTER REMARKS EVENT

          $(TABLE_NAME).on("#cmbdivision", function (e) {
                console.log('Dropdown list 1 losing focus...');
           });




       function _initKeyUpHandler($el){

         $(TABLE_NAME).on("focusout", $el, function (e) {
            cell.data(originaldata).draw();
            $($el).hide();
            TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();
           });


    // KEY UP

    $(TABLE_NAME).on("keyup", $el, function (e) {

     if (e.which === 13) {

      if ($($el).attr('class') === 'temp'){

        cell.data(originaldata).draw();


        if ($('#GroupModal').is(':visible') === true) {
          SAVE_DATA(module_name, TABLE_PARAMETER, 'manage_settings');

        } else if ($('#HolidayModal').is(':visible') === true) {
          SAVE_DATA(module_name, TABLE_PARAMETER, 'manage_settings');
        } else {
          SAVE_DATA(PAGE_NAME, rowdata, 'inventory_update');

              // console.log(rowdata);
              
              if(PAGE_NAME === 'inventory'){
                var variance = (parseInt(rowdata.sellingarea) + parseInt(rowdata.warehousearea)) - (parseInt(rowdata.onhand) + parseInt(rowdata.sellout) + parseInt(rowdata.receiving) + parseInt(rowdata.adjustment));
                UPDATE =
                {
                  "scheduleid":rowdata.scheduleid,
                  "barcode":rowdata.barcode,
                  "description":rowdata.description,
                  "scheduledate":rowdata.scheduledate,
                  "groupname":rowdata.groupname,
                  "sellingarea":rowdata.sellingarea,
                  "warehousearea":rowdata.warehousearea,
                  "statusname":rowdata.statusname,
                  "onhand":rowdata.onhand,
                  "sellout":rowdata.sellout,
                  "receiving":rowdata.receiving,
                  "adjustment":rowdata.adjustment,
                  "variance": variance,
                  "remarks2":rowdata.remarks2
                };
                TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
              }
            }

            TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();



          }else if($($el).attr('class') === 'remarks'){
     //save remarks
     var param = {remarksid:REMARKS_ID,remarks:$($el).val(),scheduleid:rowdata.scheduleid};
     console.log(param);
     SAVE_DATA(PAGE_NAME, param, 'remarks_managerecord');
            //LOAD REMARKS AND PRINT TO ROW
            REQUEST_CONTAINER = {action:'remarks_get',roleid:0,scheduleid:rowdata.scheduleid};
            var tempdata;

            LOAD_DATA(cell,function(output){
             if(typeof output === 'undefined'){ tempdata = 'NONE'; }else{ tempdata = output[0].remarks;}

             UPDATE =
             {
              "scheduleid":rowdata.scheduleid,
              "barcode":rowdata.barcode,
              "description":rowdata.description,
              "scheduledate":rowdata.scheduledate,
              "groupname":rowdata.groupname,
              "sellingarea":rowdata.sellingarea,
              "warehousearea":rowdata.warehousearea,
              "statusname":rowdata.statusname,
              "onhand":rowdata.onhand,
              "sellout":rowdata.sellout,
              "receiving":rowdata.receiving,
              "adjustment":rowdata.adjustment,
              "variance": rowdata.variance,
              "remarks2":tempdata
            };

            TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
          });

          }



        }

      });
  }

  
        $(TABLE_NAME).on('change', 'input[type="text"]', function () {
          originaldata = this.value;
        });
  
        $(TABLE_NAME).on('change', 'input[type="date"]', function () {
          originaldata = this.value;
        });
  
        $(TABLE_NAME).on('focus', 'input[type="text"]', function () {
          originaldata = this.value;
        });



       // ROW CLICK

        $(TABLE_NAME + ' tbody').on('click', 'tr', function () {
          rowdata = TBL_CONTAINER.row(this).data();
  
          ROW_DATA_FOR_UPDATE = this;
        });
  
        //CELL CLICK

        $(TABLE_NAME + ' tbody').on('click', 'td', function () {

           var xrow = TBL_CONTAINER.row($(this).closest('tr')).data();  

          if(TABLE_NAME === '#tbinventory' || TABLE_NAME === '#group_table' || TABLE_NAME === '#holiday_table' || TABLE_NAME === '#tbassignment'){
            celldata = TBL_CONTAINER.cell(this).data();

            var elementexists = celldata.includes('select');

            if (elementexists === true) {
                return;
              } 


            cellindex = TBL_CONTAINER.cell(this).index().columnVisible;
            cell = TBL_CONTAINER.cell(this);

           // console.log(PAGE_NAME);
           // console.log( $('#txtroleid').val() );

            // if($('#txtroleid').val() != '3' && PAGE_NAME === 'inventory'){
            //   return;
            // }

            if(cellindex === 3 && TABLE_NAME === '#holiday_table'){
              return;
            }

            if(cellindex === 2 && TABLE_NAME === '#group_table'){
              return;
            }
    
            var _drawCell = function(cell,element){

              if(PAGE_NAME === 'assignment'){
              var elementexists = celldata.includes('input');

              }else{
              var elementexists = celldata.includes('select');
              }


              //the select element isnt checked
              // console.log(elementexists);

    
             if (elementexists === false) {

               
                cell.data(element).draw();
                    document.getElementById($(element).attr('id')).focus();
                    ctr = ctr + 1;

                     if(PAGE_NAME === 'assignment'){
                  LOAD_DATA_IN_COMBOBOX('cmbdivision', { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname',true);
                }


              } 
            }

            switch (TABLE_NAME) {
              case '#group_table':
                if (cellindex === 0) {
                  var element = '<input type = "text" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                         _initKeyUpHandler("#txttemp" + ctr); 
                             _drawCell(cell,element);
                } else if (cellindex === 1) {
                  // var element = '<select autofocus><option value = "4">Active</option><option value = "5">Inactive</option></select>'
                }
              
                break;
              case '#holiday_table':
                if (cellindex === 0) {
                  // select.val('false').attr('selected', 'selected')
    
                  var element = '<input type = "text" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                         _initKeyUpHandler("#txttemp" + ctr); 
                             _drawCell(cell,element);
                } else if (cellindex === 1) {
                  var element = '<input type = "date" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                    _initKeyUpHandler("#txttemp" + ctr); 
                             _drawCell(cell,element);
                } else if (cellindex === 3) {
                  //  var element = '<select id = "cmbstatus"><option value = "4">Active</option><option value = "5">Inactive</option></select>';
                }
              
       
                break;

              case '#tbassignment':
      

                //the bug when the select closes its selection when selected is the select element draws again when you open the selection 

                if(cellindex === 4 || PAGE_NAME === 'assignment'){
                  var element = '<select class = "division" id = "cmbdivision"><option>aaa</option><option>bbb</option><option>ccc</option></select>'
                  // var element = '<input type = "text" class = "remarks"  id = "cmbdivision'+ctr+'" value = "' + tempremarks + '" size="6" >';
                  _initKeyUpHandler("#cmbdivision" + ctr); 
                        _drawCell(cell,element);

                        // console.log('test');
                }

              break;
              case '#tbinventory':
              REQUEST_CONTAINER = {action:'remarks_get',roleid:$('#txtroleid').val(),scheduleid:xrow.scheduleid};
              // console.log(REQUEST_CONTAINER);
              var tempdata;
              var tempremarks = '';
              // tempdata  = LOAD_DATA();

              LOAD_DATA(cell,function(output){

                if(output[0].remarks === null ){
                    tempremarks = '';
                }else{
                    tempremarks = output[0].remarks;
                }

                  REMARKS_ID = parseInt(output[0].remarksid);

                //console.log(REMARKS_ID);
                //console.log(tempremarks);
                if($('#txtroleid').val() === '3'){
                  if (cellindex === 8 || cellindex === 9) {
                    var element = '<input type = "text" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" size="4" >';
                    _initKeyUpHandler("#txttemp" + ctr); 
                          _drawCell(cell,element);
                  }
                 }


                if(cellindex === 12){
                  var element = '<input type = "text" class = "remarks"  id = "txtremarks'+ctr+'" value = "' + tempremarks + '" size="6" >';
                  _initKeyUpHandler("#txtremarks" + ctr); 
                        _drawCell(cell,element);
                }

              });

                break;
              default:
            }
            
          }
        }
        );
  
        TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();
  
      },
  
      error: function (data) {
        console.log(data);
      }
    });
  }
  
});


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                          SAVE DATA
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function SAVE_DATA(module_name, parameter, action) {
  $.ajax({
    url: "php/AdminController.php",
    method: "POST",

    data: { action: action, table_data: parameter, module_name: module_name },
    dataType: "JSON",
    success: function (data) {
      var UPDATE = null;
      //  console.log(data);

       if(action === 'insert_data'){
         if(data.requeststatusid === '0'){
            Swal.fire({
              // position: 'top-end',
              icon: 'error',
              title: data.requeststatus,
              showConfirmButton: false,
              timer: 3000
            })
         }
       }
 
       if(action === 'inventory_post' || action === 'inventory_approve'){
  
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'Your work has been saved',
          showConfirmButton: false,
          timer: 1500
        })
  
        TBL_CONTAINER.clear().draw();
       }


      // if (data.id != 0) {

      //   switch(module_name) {
      //     case "group":
      //         UPDATE =
      //         {
      //           "groupid": data.id,
      //           "groupname": rowdata.groupname,
      //           "statusname": "Active",
      //           "recordstatus": 4
      //         };
      //       break;
      //     case "holiday":
      //         UPDATE =
      //         {
      //           "holidayid": data.id,
      //           "holidayname": rowdata.holidayname,
      //           "holidaydate": rowdata.holidaydate,
      //           "statusname": "Active",
      //           "recordstatus": 4
      //         };
      //       break;
      //     default:
      //       // code block
      //   }

      // }

      if(PAGE_NAME === 'user'){
        if($('#txtusername').val() === '0'){
          TBL_CONTAINER.row.add({
            "userid": data.id,
            "username": $('#txtusername').val(),
            // "userpassword": $('#txtpassword').val(),
            "personnelname": $('#txtpersonnel').val(),
            "roleid": $('#cmbrole').val(),
            "categoryid": $('#cmbcategory').val(),
            "recordstatus": 1,
            "rolename": $("#cmbrole option:selected").html(),
            "categoryname": $("#cmbcategory option:selected").html(),
            "branchname": $("#cmbbranch option:selected").html(),
            "statusname": "Active",
            "branchid": $('#cmbbranch').val()
          }).draw();
        }else{
          UPDATE =
          {
            "userid": rowdata.userid,
            "username": $('#txtusername').val(),
            // "userpassword": $('#txtpassword').val(),
            "personnelname": $('#txtpersonnel').val(),
            "roleid": $('#cmbrole').val(),
            "categoryid": $('#cmbcategory').val(),
            "recordstatus": $('#cmbstatus').val(),
            "rolename": $("#cmbrole option:selected").html(),
            "categoryname": $("#cmbcategory option:selected").html(),
            "branchname": $("#cmbbranch option:selected").html(),
            "statusname":  $("#cmbstatus option:selected").html(),
            "branchid": $('#cmbbranch').val()
          };
        }
      }else if (PAGE_NAME === 'login'){
        console.log(data.roleid);
        if (data.requeststatus === 1){
          if(data.roleid != '4'){
            window.location.replace('inventory.html');
          }
          else if (data.roleid === '4'){
            window.location.replace('index.html');
          }
        }else{
          Swal.fire({
            // position: 'top-end',
            icon: 'error',
            title: 'The Credentials you entered doesnt match any account',
            showConfirmButton: false,
            timer: 1500
          })
    
        }
      }
     
      if(UPDATE != null){
        TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
      }

     
     

    },
    complete: function (data) {
      console.log('saving data complete!');
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function ADD_NEW_ROW() {
  if (module_name === "group") {
    TBL_CONTAINER.row.add({
      "groupid": "0",
      "groupname": "",
      "statusname": "Active",
      "recordstatus": "4",
    }).draw();
  }
  else if (module_name === "holiday") {
    TBL_CONTAINER.row.add({
      "holidayid": "0",
      "holidayname": "",
      "holidaydate": "",
      "statusname": "Active",
      "recordstatus": "4",
    }).draw();
  }
}

function GET_PAGENAME() {
  var path = window.location.pathname;
  var page = path.split("/").pop();
  var page = page.replace('.html', '');


  return page;
}

function GET_DATE_NOW(){
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, '0');
  var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
  var yyyy = today.getFullYear();
  
  // today = mm + '/' + dd + '/' + yyyy;
  today = yyyy + '-' + mm + '-' + dd;

  // console.log(today);
  return today;
}

function LOAD_DATA_IN_COMBOBOX(element,parameter,phpfile,col1,col2,addempty = false){
      $.ajax({
                  url:phpfile,
                  method:"POST",
                  data :parameter,
                  dataType: "JSON",
                  success:function(data)
                  {
                    $.ctr = 0;

                    var select = document.getElementById(element);
                    
              
                    select.innerHTML = "";
                    while($.ctr < data.length)
                    {

                        var option = document.createElement('option');
                        option.value = data[$.ctr][col1];
                        option.text = data[$.ctr][col2];
                        select.add(option, $.ctr);
                        $.ctr = $.ctr + 1;
                    }

                    if(addempty === true){
                      $('#' + element).prepend("<option value='' selected='selected'></option>");
                    }
                    
                  },
                     error: function(data) 
                     {
                       console.log(data);
                        // console.log('LOAD_DATA_IN_COMBOBOX -- error');
                     },
      });
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//                        ACCESS RIGHTS
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function LOAD_NAV_DATA(data){

    setTimeout(function(){ 
      $('#txtbranchid').val(data.branchid);
      $('#nvusername').text(data.personnelname);
      $('#nvbranch').text(data.branchname);
      $('#txtcategoryid').val(data.categoryid);
      $('#txtroleid').val(data.roleid);
    }, 500)

    setTimeout(function(){ 
      if($('#txtroleid').val() === '1' ){
        //tl
        //Tig approve ng mga posted na products
        // hide ma ibang link
        $("#lnkindex").hide();
        $("#lnkreport").hide();
        $("#lnkuser").hide();
        $("#lnksettings").hide();
        $("#approved").hide();
        $("#onhand").hide();
        $("#lnkgroup").hide();
        $("#lnkitem").hide();

        $('#posted').click()
        $("#liposted").attr("class","active");

        $("#btnapprove").attr("disabled", true);
        $("#btnpost").attr("disabled", false);


        if(PAGE_NAME != 'inventory'){
          window.location.replace('inventory.html');
        }


      }
  
      if($('#txtroleid').val() === '2' ){
        //atl
        //Tig approve ng mga posted na products
        $("#approved").hide();
        $("#onhand").hide();
        $("#lnkuser").hide();
        $("#lnkindex").hide();
        $("#lnkreport").hide();
        $("#lnkitem").hide();
        $("#lnksettings").hide();
        $("#lnkgroup").hide();
       // hide ma ibang link
       $('#posted').click()
       $("#liposted").attr("class","active");
       console.log('btnapprove!!!');
       $("#btnapprove").attr("disabled", false);
       $("#btnpost").attr("disabled", true);

       if(PAGE_NAME != 'inventory'){
        window.location.replace('inventory.html');
      }

       
      }
  
      if($('#txtroleid').val() === '3' ){
        //encoder
        $("#lnkuser").hide();
        $("#lnkindex").hide();
        $("#lnkreport").hide();
        $("#lnksettings").hide();
        $("#lnkgroup").hide();
        $("#lnkitem").hide();
        $('#onhand').click()
        $("#lionhand").attr("class","active");
        $("#approved").hide();
        $("#posted").hide();

        $("#btnapprove").attr("disabled", true);
        $("#btnpost").attr("disabled", false);

        if(PAGE_NAME != 'inventory'){
          window.location.replace('inventory.html');
        }

      }
  
      if($('#txtroleid').val() === '4' ){
        //ho cic decoarts
        $("#onhand").hide();
        $("#posted").hide();

        $("#btnapprove").attr("disabled", true);
        $("#btnpost").attr("disabled", true);

        $('#approved').click()
        $("#liapproved").attr("class","active");

      }
 
    }, 500)
    
  }

  function USER_CONTROL(action,callback){
  $.ajax({
    // user_check
    // user_logout

    url: "php/AdminController.php",
    method: "POST",
    async: false,
    data: { action: action},
    dataType: "JSON",
    success:function(data)
    {
      if(action === 'user_check'){
        callback(data);
        CATEGORY_NAME = data.categoryname;
        return BRANCH_ID = data.branchid;

      }else if (action === 'user_logout'){
        window.location.replace('login.html');
      }

    },
    error: function(data) 
    {
      window.location.replace('login.html');
    },
  });
}

$(document).on("click", "#btnlogin", function () {
  SAVE_DATA(PAGE_NAME,{username: $('#txtusername').val(),password: $('#txtpassword').val()},'user_authenticate');
});

$(document).on('change', "#cmbcategory", function () {
  if(PAGE_NAME === 'item'){
    var val = $(this).val();
    TBL_CONTAINER.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
  }
});

$(document).on('change', "#cmbsupplier", function () {
  var val = $(this).val();
    TBL_CONTAINER.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
});

$(document).on('change', "#cmbbranch", function () {

  if(PAGE_NAME === 'index'){
    var val = $(this).val();
    TBL_CONTAINER.column(15).search(val ? '^' + val + '$' : '', true, false).draw();
  }

});


$(document).on('keypress', "#fltbarcode", function () {
  var val = $(this).val();
    TBL_CONTAINER.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
});

$(document).on('keypress', "#fltdescription", function () {
  var val = $(this).val();
    TBL_CONTAINER.column(2).search(val ? '^' + val + '$' : '', true, false).draw();
});

function LoadingControl() {
  var x = document.getElementById('loader');
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function LOAD_DATA(cell,handledata){

// console.log(REQUEST_CONTAINER);

   $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: REQUEST_CONTAINER,
    dataType: "JSON",
    beforeSend: function() {
       LoadingControl();
    },
    success: function(data) {
      LoadingControl();
     handledata(data); 

    },
    error: function(data) {
      LoadingControl();
      console.log(data);
    }
    
  });

}

function LOAD_LEDGER(){
  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: REQUEST_CONTAINER,
    dataType: "JSON",
    beforeSend: function() {
      LoadingControl();
    },
    success: function(data) {
      console.log(data);
       var row = '';
      for(var i = 0; i < data.length; i++) {
        var obj = data[i];
        row +=  "<tr><td>"+obj.scheduledate+"</td><td>"+obj.onhand+"</td><td>"+obj.sellout+"</td><td>"+obj.adjustment+"</td><td>"+obj.warehousearea+"</td><td>"+obj.reservation+"</td><td>"+obj.variance+"</td><td>"+obj.variance+"</td><td>"+obj.remarks+"</td></tr>";
    }
    $('#ledgerbody').html(row);

    },
    error: function(data) {
      console.log(data);
    },
    complete: function() {
      LoadingControl();
    }
    
  });
}

