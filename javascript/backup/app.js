var TBL_CONTAINER;
var COLUMN_CONTAINER = [];
var COLUMN_DEFS = [];
var REQUEST_CONTAINER;
var GROUP_PARAMETER = { action: 'load_group_settings',isused: null };
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
var xrow;
var CURRENT_PAGE;
var celldata;
var cell;
var originaldata;
var cellindex = 0;
var selected_division = [];
var DEPARTMENT;

var SYS_GROUP_COLUMNS = [];
SYS_GROUP_COLUMNS[0] = { "data": "groupid" };
SYS_GROUP_COLUMNS[1] = { "data": "groupname",  orderable: false };
SYS_GROUP_COLUMNS[2] = { "data": "statusname",  orderable: false };
SYS_GROUP_COLUMNS[3] = {  
  targets: 3,
  data: null,
  render: function ( data, type, row ) {
    
    if(data.isUsed === '1'){
      retdata = "<button type='button' id = 'btngroupdeactivate' class='btn btn-info' disabled>Remove</button>";
    }else{
      retdata = "<button type='button' id = 'btngroupdeactivate' class='btn btn-info'>Remove</button>";
    }
    return  retdata ;
  }
  
};


var GROUP_COLUMNDEFS = [];
GROUP_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
GROUP_COLUMNDEFS[1] = { "width": "50px", "targets": 3 };


var HOLIDAY_COLUMNS = [];
HOLIDAY_COLUMNS[0] = { "data": "holidayid" };
HOLIDAY_COLUMNS[1] = { "data": "holidayname",  orderable: false, };
HOLIDAY_COLUMNS[2] = { "data": "holidaydate",  orderable: false, };
HOLIDAY_COLUMNS[3] = { "data": "statusname",  orderable: false, };
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


var DATABASE_COLUMNS = [];
DATABASE_COLUMNS[0] = { "data": "storeid" };
DATABASE_COLUMNS[1] = { "data": "description" };
DATABASE_COLUMNS[2] = { "data": "logdate" };

var DATABASE_COLUMNDEFS = [];
DATABASE_COLUMNDEFS[0] = { "width": "50%", "targets": 1 };
DATABASE_COLUMNDEFS[1] = { "targets": [0], "visible": false, "searchable": false };



var INVENTORY_COLUMNS = [];
INVENTORY_COLUMNS[0] = { "data": "scheduleid" };
INVENTORY_COLUMNS[1] = { "data": "barcode" };
INVENTORY_COLUMNS[2] = { "data": "legacybarcode" };
INVENTORY_COLUMNS[3] = { "data": "description",

render: function ( data, type, row ) {
  
    return retdata = "<div style = 'width:200px;'>"+ data+"</div>";
}

};
INVENTORY_COLUMNS[4] = { "data": "scheduledate",

render: function ( data, type, row ) {
  
    return retdata = "<div style = 'width:90px;'>"+ data+"</div>";
}

};
INVENTORY_COLUMNS[5] = { "data": "groupname" };
INVENTORY_COLUMNS[6] = { "data": "onhand" };
INVENTORY_COLUMNS[7] = { "data": "sellout" };
INVENTORY_COLUMNS[8] = { "data": "receiving" };
INVENTORY_COLUMNS[9] = { "data": "rcv_ir" };
INVENTORY_COLUMNS[10] = { "data": "rcv_er" };
INVENTORY_COLUMNS[11] = { "data": "adj_issue" };
INVENTORY_COLUMNS[12] = { "data": "adj_receipt" };
INVENTORY_COLUMNS[13] = { "data": "sellingarea" };
INVENTORY_COLUMNS[14] = { "data": "warehousearea" };
INVENTORY_COLUMNS[15] = { "data": "variance" };
INVENTORY_COLUMNS[16] = { "data": "statusname" };
INVENTORY_COLUMNS[17] = { "data": "remarks" };
INVENTORY_COLUMNS[18] = { "data": "tlremarks" };
INVENTORY_COLUMNS[19] = { "data": "atlremarks" };


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
INVENTORY_COLUMNDEFS[13] = { targets: 6, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[14] = { targets: 7, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[15] = { targets: 8, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[16] = { targets: 9, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[17] = { targets: 10, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[18] = { targets: 11, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[19] = { targets: 13, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[20] = { targets: 14, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[21] = { targets: 15, className: 'dt-body-right' };
INVENTORY_COLUMNDEFS[22] = { targets: 12, className: 'dt-body-right' };


var ITEM_COLUMNS = [];
ITEM_COLUMNS[0] = { "data": "barcode" };
ITEM_COLUMNS[1] = { "data": "legacybarcode" };
ITEM_COLUMNS[2] = { "data": "description" };
ITEM_COLUMNS[3] = { "data": "brandname" };
ITEM_COLUMNS[4] = { "data": "divisionname" };
ITEM_COLUMNS[5] = { "data": "itemtype" };
ITEM_COLUMNS[6] = { "data": "itemtypename" };
ITEM_COLUMNS[7] = { 
  targets: 7,
  data: null,
  render: function ( data, type, row ) {
    var retdata;
    return retdata = "<button type='button' id = 'btnmdschedule' class='btn btn-info'>Add</button>";
  }
};
ITEM_COLUMNS[8] = { "data": "storeid" };


var ITEM_COLUMNDEFS = [];
ITEM_COLUMNDEFS[0] =  { "width": "5%", "targets": 0 };
ITEM_COLUMNDEFS[1] =  { "width": "30%", "targets": 1 };
ITEM_COLUMNDEFS[2] =  { "width": "25%", "targets": 2 };
ITEM_COLUMNDEFS[3] =  { "width": "15%", "targets": 3 };
ITEM_COLUMNDEFS[4] =  { "width": "10%", "targets": 4 };
ITEM_COLUMNDEFS[5] =  { "targets": [5], "visible": false, "searchable": false };
ITEM_COLUMNDEFS[6] =  { "targets": [8], "visible": false, "searchable": false };


var ITEM_CANCEL_COLUMNS = [];
ITEM_CANCEL_COLUMNS[0] = { "data": "scheduleid" };
ITEM_CANCEL_COLUMNS[1] = { "data": null };
ITEM_CANCEL_COLUMNS[2] = { "data": "barcode" };
ITEM_CANCEL_COLUMNS[3] = { "data": "legacybarcode" };
ITEM_CANCEL_COLUMNS[4] = { "data": "description" };
ITEM_CANCEL_COLUMNS[5] = { "data": "brandname" };
ITEM_CANCEL_COLUMNS[6] = { "data": "divisionname" };
ITEM_CANCEL_COLUMNS[7] = { "data": "storename" };
ITEM_CANCEL_COLUMNS[8] = { "data": "groupname" };
ITEM_CANCEL_COLUMNS[9] = { "data": "scheduledate" };
// ITEM_CANCEL_COLUMNS[9] = { 
//   targets: 9,
//   data: null,
//   render: function ( data, type, row ) {
//     return retdata = "<button type='button' id = 'btnrowcancel' class='btn btn-info'>Cancel</button>";
//   }
// };

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
ITEM_CANCEL_COLUMNDEFS[2] = { "width": "20px", "targets": 0 };
ITEM_CANCEL_COLUMNDEFS[3] = { "width": "20px", "targets": 1 };
ITEM_CANCEL_COLUMNDEFS[4] = { "width": "10%", "targets": 2 };
ITEM_CANCEL_COLUMNDEFS[5] = { "width": "40%", "targets": 4 };
ITEM_CANCEL_COLUMNDEFS[6] = { "width": "20%", "targets": 5 };
ITEM_CANCEL_COLUMNDEFS[7] = { "width": "10%", "targets": 6 };
ITEM_CANCEL_COLUMNDEFS[8] = { "width": "10%", "targets": 9 };

var ITEM_CHILD_COLUMNS = [];
ITEM_CHILD_COLUMNS[0] = { "data": "scheduleid" };
ITEM_CHILD_COLUMNS[1] = { "data": "barcode" };
ITEM_CHILD_COLUMNS[2] = { "data": "legacybarcode" };
ITEM_CHILD_COLUMNS[3] = { "data": "description" };
ITEM_CHILD_COLUMNS[4] = { "data": "brandname" };
ITEM_CHILD_COLUMNS[5] = { "data": "divisionname" };
ITEM_CHILD_COLUMNS[6] = { "data": "storename" };
ITEM_CHILD_COLUMNS[7] = { "data": "groupname" };
ITEM_CHILD_COLUMNS[8] = { "data": "scheduledate" };
ITEM_CHILD_COLUMNS[9] = { "data": "transactiondate" };

var ITEM_CHILD_COLUMNDEFS = [];
ITEM_CHILD_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
ITEM_CHILD_COLUMNDEFS[1] = { "width": "20%", "targets": 2 };
ITEM_CHILD_COLUMNDEFS[2] = { "width": "40%", "targets": 3 };
ITEM_CHILD_COLUMNDEFS[3] = { "width": "10%", "targets": 4 };

var INDEX_COLUMNS = [];
INDEX_COLUMNS[0] = { "data": "scheduleid" };
INDEX_COLUMNS[1] = { "data": "barcode" };
INDEX_COLUMNS[2] = { "data": "legacybarcode" };
INDEX_COLUMNS[3] = { "data": "description",

render: function ( data, type, row ) {
  
    return retdata = "<div style = 'width:200px;'>"+ data+"</div>";
}

 };
INDEX_COLUMNS[4] = { "data": "scheduledate",

  render: function ( data, type, row ) {
    
      return retdata = "<div style = 'width:90px;'>"+ data+"</div>";
  }

};
INDEX_COLUMNS[5] = { "data": "storename" };
INDEX_COLUMNS[6] = { "data": "groupname" };
INDEX_COLUMNS[7] = { "data": "onhand" };
INDEX_COLUMNS[8] = { "data": "sellout" };
INDEX_COLUMNS[9] = { "data": "receiving" };
INDEX_COLUMNS[10] = { "data": "rcv_ir" };
INDEX_COLUMNS[11] = { "data": "rcv_er" };
INDEX_COLUMNS[12] = { "data": "adj_issue" };
INDEX_COLUMNS[13] = { "data": "adj_receipt" };
INDEX_COLUMNS[14] = { "data": "sellingarea" };
INDEX_COLUMNS[15] = { "data": "warehousearea" };
INDEX_COLUMNS[16] = { "data": "variance" };
INDEX_COLUMNS[17] = { "data": "statusname" };
INDEX_COLUMNS[18] = { 
  targets: 18,
  data: null,
  render: function ( data, type, row ) {
    return retdata = "<button type='button' id = 'btnledger' class='btn btn-info'>Ledger</button>";
  }
};

INDEX_COLUMNS[19] = { "data": "storeid" };


var INDEX_COLUMNDEFS = [];
INDEX_COLUMNDEFS[0] = { "targets": [0], "visible": false, "searchable": false };
INDEX_COLUMNDEFS[1] = { "width": "7%", "targets": 1 };
INDEX_COLUMNDEFS[2] = { "width": "20%", "targets": 2 };
INDEX_COLUMNDEFS[3] = { "width": "20%", "targets": 3 };
INDEX_COLUMNDEFS[4] = { "width": "10%", "targets": 4 };
INDEX_COLUMNDEFS[5] = { "width": "10%", "targets": 5 };
INDEX_COLUMNDEFS[6] = { "width": "7%", "targets": 6 };
INDEX_COLUMNDEFS[7] = { "width": "7%", "targets": 7 };
INDEX_COLUMNDEFS[8] = { "width": "7%", "targets": 8 };
INDEX_COLUMNDEFS[9] = { "width": "7%", "targets": 9 };
INDEX_COLUMNDEFS[10] = { "width": "7%", "targets": 10 };
INDEX_COLUMNDEFS[11] = { "width": "7%", "targets": 11 };
INDEX_COLUMNDEFS[12] = { targets: 13, className: 'dt-body-right' };
INDEX_COLUMNDEFS[13] = { targets: 7, className: 'dt-body-right' };
INDEX_COLUMNDEFS[14] = { targets: 8, className: 'dt-body-right' };
INDEX_COLUMNDEFS[15] = { targets: 9, className: 'dt-body-right' };
INDEX_COLUMNDEFS[16] = { targets: 10, className: 'dt-body-right' };
INDEX_COLUMNDEFS[17] = { targets: 11, className: 'dt-body-right' };
INDEX_COLUMNDEFS[18] = { targets: 12, className: 'dt-body-right' };
INDEX_COLUMNDEFS[19] = { targets: 13, className: 'dt-body-right' };
INDEX_COLUMNDEFS[20] = { targets: 14, className: 'dt-body-right' };
INDEX_COLUMNDEFS[21] = { targets: 15, className: 'dt-body-right' };
INDEX_COLUMNDEFS[22] = { targets: 16, className: 'dt-body-right' };
INDEX_COLUMNDEFS[23] = { "targets": [19], "visible": false, "searchable": true };

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
USER_COLUMNS[0] = {"data": "employee_number"};
USER_COLUMNS[1] = {"data": "full_name"};
USER_COLUMNS[2] = {"data": "job_description"};
USER_COLUMNS[3] = {"data": "assigned_store"};
USER_COLUMNS[4] = {"data": "divisionname"};
USER_COLUMNS[5] = { 
  targets: 5,
  data: null,
  render: function ( data, type, row ) {
    return retdata = "<button type='button' id = 'btnuseredit' class='btn btn-info'>Edit</button>";
  }
  
};

USER_COLUMNS[6] = {"data": "store_id"};
USER_COLUMNS[7] = {"data": "departments"};
USER_COLUMNS[8] = {"data": "job_code"};
USER_COLUMNS[9] = {"data": "userdivisionid"};

var USER_COLUMNDEFS = [];
USER_COLUMNDEFS[0] = { "targets": [6], "visible": false, "searchable": false };
USER_COLUMNDEFS[1] = { "targets": [7], "visible": false, "searchable": false };
USER_COLUMNDEFS[2] = { "width": "30%","targets": [1] };
USER_COLUMNDEFS[3] = { "width": "20%","targets": [2]};
USER_COLUMNDEFS[4] = { "width": "20%","targets": [4]};
USER_COLUMNDEFS[5] = { "targets": [8], "visible": false, "searchable": false };
USER_COLUMNDEFS[6] = { "targets": [9], "visible": false, "searchable": false };

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
  $("#modalcontainer3").load("changepass.html");

  
  PAGE_NAME = GET_PAGENAME();
  if(PAGE_NAME != 'login'){
    
    document.getElementById("loader").style.display = "none";
    USER_CONTROL('user_check',LOAD_NAV_DATA);
    
  }
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //  SET ONLOAD VALUE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  if(PAGE_NAME === 'group'){

    var tomorrow =  addDays(new Date(),1);
    var dd = String(tomorrow.getDate()).padStart(2, '0');
    var mm = String(tomorrow.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = tomorrow.getFullYear();
    tomorrow = yyyy + '-' + mm + '-' + dd;

    var dateToday = GET_DATE_NOW();
    document.getElementById("txtgrpdate").min = tomorrow; 
    document.getElementById("txtgrpdate").value = tomorrow;
    
    document.getElementById("txtgrpenddate").min = tomorrow; 
    document.getElementById("txtgrpenddate").value = tomorrow;

    var dateDiff = getDateDiff($('#txtgrpdate').val(),$('#txtgrpenddate').val());

    if(dateDiff === 0){
      $('#cmbcountperyear').html('<option>1</option>')
    }else if(dateDiff == 1){
      $('#cmbcountperyear').html('<option>1</option><option>2</option>')
    }else{
      $('#cmbcountperyear').html('<option>1</option><option>2</option><option>3</option>')
    }
    
    
  }
  else if(PAGE_NAME === 'productschedule'){
    
    var dateToday = GET_DATE_NOW();
    document.getElementById("dtfrom").value = dateToday; 
    document.getElementById("dtto").value = dateToday;
  }else if(PAGE_NAME === 'database'){


     var yesterday =  addDays(new Date(),-1);
    var dd = String(yesterday.getDate()).padStart(2, '0');
    var mm = String(yesterday.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = yesterday.getFullYear();
    yesterday = yyyy + '-' + mm + '-' + dd;

    document.getElementById("txtdatefrom").max = yesterday; 
    document.getElementById("txtdateto").max = yesterday; 
    
    // var dateToday = GET_DATE_NOW();
    document.getElementById("txtdatefrom").value = yesterday; 
    document.getElementById("txtdateto").value = yesterday;
  }
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //  BUTTON CLICK EVENT
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  $(document).on('click', "#btn_index_search", function () { 
    
    if($('#licurrent').attr("class") === 'active'){
      REQUEST_CONTAINER = { action: 'index_currentcount',groupid:$('#cmbgroup').val(), statusid: 6,categoryname:CATEGORY_NAME,datefrom:document.getElementById("dt_index_datefrom").value,dateto:document.getElementById("dt_index_dateto").value,storeid:$('#cmbbranch').val()};
      COLUMN_CONTAINER = INDEX_COLUMNS;
      COLUMN_DEFS = INDEX_COLUMNDEFS;
      LOAD_DATATABLE('#tb' + PAGE_NAME);
    }else{
      LOAD_INDEX(); 
    }
    
  });
  
  $(document).on("click", "#btnsearchitem", function () { 
    
    if($('#fltbarcode').val() != '' && $('#cmbbranch').val() != '0' ){
      LOAD_ITEM(); 
      
    }else{
      Swal.fire({
        // position: 'top-end',
        icon: 'error',
        title: 'please enter barcode',
        showConfirmButton: false,
        timer: 3000
      })
    }
    
  });
  
  $(document).on("click", "#btnadditem", function () { $('#addItemLegacy').modal('show'); });
  $(document).on("click", "#btnsearchinventory", function () { LOAD_INVENTORY(); });
  $(document).on("click", "#btnprint", function () { $(".buttons-print")[0].click(); });
  $(document).on("click", "#btnexport", function () { $(".buttons-excel")[0].click(); });
  $(document).on("click", "#btnadd", function () { ADD_NEW_ROW(); });

  $(document).on('click', "#btnxyz", function () { 
    
    SAVE_DATA(null, {barcode:'1090100660005',scheduledate:'2020-09-21'}, 'import_store_settings_V2',null);
    
  });
  
  $(document).on("click", "#btngenerateschedule", function () {

    if(PAGE_NAME === 'productschedule'){
      REQUEST_CONTAINER = {action: 'rpt_schedule',datefrom: $('#dtfrom').val() , dateto:$('#dtto').val(),groupid:$('#cmbgroup').val(),storeid:$('#cmbbranch').val(),statusid:$('#cmbstatus').val()};
      COLUMN_CONTAINER = INVENTORY_COLUMNS;
      COLUMN_DEFS = INVENTORY_COLUMNDEFS;
      LOAD_DATATABLE('#tb' + PAGE_NAME);
    }else if(PAGE_NAME === 'scheduledgroup'){
      REQUEST_CONTAINER = {action: 'rpt_scheduled_group',groupid:$('#cmbgroup').val(),storeid:$('#cmbbranch').val(),statusid:$('#cmbstatus').val()};
      COLUMN_CONTAINER = INVENTORY_COLUMNS;
      COLUMN_DEFS = INVENTORY_COLUMNDEFS;
      LOAD_DATATABLE('#tb' + PAGE_NAME);
    }

  });
  
  $(document).on("click", "#btnupdate", function () {
    // if($('#cmbbranch').val() === '105' || $('#cmbbranch').val() === '156' || $('#cmbbranch').val() === '143' || $('#cmbbranch').val() === '415' || $('#cmbbranch').val() === '320' || $('#cmbbranch').val() === '110' || $('#cmbbranch').val() === '129' || $('#cmbbranch').val() === '150' || $('#cmbbranch').val() === '137' || $('#cmbbranch').val() === '148'){
        
      if($('#txtdateto').val() < GET_DATE_NOW()){
        SAVE_DATA(null, {storeid:$('#cmbbranch').val(),storename:$('#cmbbranch option:selected').html(),datefrom:$('#txtdatefrom').val(),dateto:$('#txtdateto').val()}, 'database_update',LOAD_DATABASE);
      }
    else{
      Swal.fire({
        // position: 'top-end',
        icon: 'error',
        title: 'Please enter the current date to proceed',
        showConfirmButton: false,
        timer: 3000
      })
    }
    
  });
  
  $(document).on("click", "#btnexportledger", function (event) {
    
    event.preventDefault();
    $('#txttableContainer').val($('#tbledger').html());
    
    setTimeout(function(){  
      document.getElementById("exportTable").submit();
    }, 2000);
    
  });
  
  $(document).on("click", "#btnsearchass", function () {
    
    REQUEST_CONTAINER = { action: PAGE_NAME,storeid:$('#cmbbranch').val(),roleid:$('#fltrole').val()};
    COLUMN_CONTAINER = ASSIGNMENT_COLUMNS;
    COLUMN_DEFS = ASSIGNMENT_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
    
  });
  
  $(document).on("click", "#btnsearchindex", function () {
    
    REQUEST_CONTAINER = { action: PAGE_NAME,assignment: $("#cmbbranch option:selected").html(),departments:$("#cmbdepartment option:selected").html(),storeid:$("#cmbbranch").val()};
    COLUMN_CONTAINER = USER_COLUMNS;
    COLUMN_DEFS = USER_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
    
  });
  
  $(document).on("click", "#btnsaveass", function () {
    
    var newArray = TABLE_PARAMETER.filter(function (el) {
      return el.division_name != 'NOT SET';
    });
    
    SAVE_DATA(PAGE_NAME, newArray, 'assignment_manage');
    REQUEST_CONTAINER = { action: PAGE_NAME,storeid:$('#cmbbranch').val(),roleid:$('#fltrole').val()};
    COLUMN_CONTAINER = ASSIGNMENT_COLUMNS;
    COLUMN_DEFS = ASSIGNMENT_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
    
  });
  
  $( "#btnsearch" ).click(function() { 
    SEARCH_ITEM(); 
    $("#btncancel").show();
  });
  
  // btncancel
  
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
  
  $(document).on("click", "#btnrowcancel", function () {
    TBL_CONTAINER.rows().deselect();
    
    var data = TBL_CONTAINER.rows().data();
    data.each(function (value, index) {
      $('.dt-checkboxes-cell').closest('tr').find("input").prop('checked', false)
    });
    
    $(this).closest('tr').find("input").prop('checked', true)
    TBL_CONTAINER.rows(ROW_DATA_FOR_UPDATE).select();
    $('#cancelModal').modal('show');
  });
  
  $(document).on("click", "#btncancel", function () {
    $('#cancelModal').modal('show');
  });
  
  $(document).on("click", "#btnYes", function () {
    var data = TBL_CONTAINER.rows( ['.even.selected', '.odd.selected'] ).data().toArray();
    // console.log(data);
    SAVE_DATA(PAGE_NAME, data, 'item_cancel',INSERT_REASON);
  });
  
  $(document).on("click", "#btnNo", function () {
    $('#cancelModal').modal('hide');
  });
  
  $(document).on("click", "#btnuseredit", function () {
    
    $('#txtusername').val('');
    $('#txtpassword').val('');
    
    $('#txtempid').val(rowdata.employee_number); 
    $('#txtfullname').val(rowdata.full_name); 
    $('#txtrole').val(rowdata.job_description); 
    $('#txtuserdivisionid').val(rowdata.userdivisionid); 

    if(rowdata.departments === 'NOT SET'){
      $('#txtassignment').val(rowdata.assigned_store);
    }else{
      $('#txtassignment').val(rowdata.departments);
    }
    
    if($('#cmbbranch').val() != '-1'){
      $('#txtusername').val(rowdata.employee_number); 
      $('#txtpassword').val(rowdata.password); 
    }
    
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
    var data = $('#cmbdivision').select2('data')
    selected_division = [];
    data.forEach(val =>  selected_division.push(val.text));
    
    var statusid;
    if($('#cbstatus').is(":checked") === true){
      statusid = 4;
    }else{
      statusid = 5;
    }

// console.log(statusid);
    // Encoder
    // Team Leader (TL)
    // Asst. Team Leader(ATL)
    if($('#txtrole').val() === 'Encoder' || $('#txtrole').val() === 'Team Leader (TL)' || $('#txtrole').val() === 'Asst. Team Leader(ATL)' || $('#txtrole').val() === 'Store Admin Assistant' || rowdata.assigned_store === 'Main Office'){
      if($('#txtusername').val() != '' && $('#txtpassword').val() != ''){

        parameter = {
          emp_id: $('#txtempid').val() ,divisionid: [0,0],divisionname: selected_division.join(),
          storeid:rowdata.store_id,username:$('#txtusername').val(),password:$('#txtpassword').val(),
          userdivisionid:$('#txtuserdivisionid').val(),full_name:$('#txtfullname').val(),store_name:rowdata.assigned_store,
          department:$('#txtassignment').val(),rolename:$('#txtrole').val(),job_code:rowdata.job_code,statusid:statusid
        };
        console.log(parameter);
        SAVE_DATA(PAGE_NAME,parameter,'user_managerecord');
        
      }else{
        Swal.fire({
          // position: 'top-end',
          icon: 'error',
          title: 'please fill up the missing field',
          showConfirmButton: false,
          timer: 3000
        })
      }
      
    }else{
      
      if($('#cmbdivision').val().length != 0 && $('#txtusername').val() != '' && $('#txtpassword').val() != ''){
        parameter = {
          emp_id: $('#txtempid').val() ,divisionid: $('#cmbdivision').val(),divisionname: selected_division.join(),
          storeid:rowdata.store_id,username:$('#txtusername').val(),password:$('#txtpassword').val(),
          userdivisionid:$('#txtuserdivisionid').val(),full_name:$('#txtfullname').val(),store_name:rowdata.assigned_store,
          department:$('#txtassignment').val(),rolename:$('#txtrole').val(),job_code:rowdata.job_code,statusid:statusid
        };
        console.log(parameter);
        SAVE_DATA(PAGE_NAME,parameter,'user_managerecord');
        
      }else{
        Swal.fire({
          // position: 'top-end',
          icon: 'error',
          title: 'please fill up the missing field',
          showConfirmButton: false,
          timer: 3000
        })
      }
    }
  });
  
  $(document).on("click", "#btnchangepass", function () {
    parameter = {oldpassword: $('#txtoldpassword').val(),newpassword: $('#txtnewpassword').val()};
    SAVE_DATA(PAGE_NAME,parameter,'change_pass');
  });

  $(document).off("click", "button").on("click", "button", function () {
    
    switch(this.id) {
      case 'btnmdschedule':
      //clear the values in the modal
      $("#cmbgroup3").html(""); 
      var datetoday  = GET_DATE_NOW();
      $('#lbdescription').html(rowdata.description);
      $('#lbbarcode').html(rowdata.barcode); 
      $('#lbitemtype').html(rowdata.itemtypename); 
      $('#txtitemtypeid').val(rowdata.itemtype); 

      // console.log(rowdata);
      LOAD_DATA_IN_COMBOBOX('cmbbranch3',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      $('#cmbbranch3').select2();

      setTimeout(function(){
        $('#cmbbranch3').val(rowdata.storeid).trigger("change");
        $('#cmbbranch3').prop('disabled', true); 
      }, 3000);

      $('#cmbgroup3').select2();
      $('#myModal').modal('show');
      
      var det =  addDays(new Date(),1);
      var today = det;
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      today = yyyy + '-' + mm + '-' + dd;
      document.getElementById("txtdate").min = today; 
      $('#txtdate').val(today); 
      
      break;
      
      case 'btnadditem':
      
      // LOAD_DATA_IN_COMBOBOX('cmbgroup4', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');
      LOAD_DATA_IN_COMBOBOX('cmbbranch4',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      
      $('#cmbbranch4').select2();
      $('#cmbgroup4').select2();
      
      var det =  addDays(new Date(),1);
      var today = det;
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      today = yyyy + '-' + mm + '-' + dd;
      document.getElementById("txtdate2").min = today; 
      $('#txtdate2').val(today); 
      
      
      break;
      
      case 'btnadditemlegacy':

        REQUEST_CONTAINER = {action:'check_holiday',datedesc:$('#txtdate2').val()};
        LOAD_DATA(cell,function(output){
          
          if(output.length === 0){

            if($('#cmbgroup4').val() && $('#txtbarcode').val() && $('#txtdescription').val()){
        
              var parameter = {
                scheduledate:$('#txtdate2').val(),
                productid:$('#txtproductid').val(),
                groupid:$('#cmbgroup4').children("option:selected").val(),
                barcode: $('#txtbarcode').val(),
                storeid:$('#cmbbranch4').val(),
                description:$('#txtdescription').val()
              };
              
              SAVE_DATA(module_name, parameter , 'item_add_schedule_phaseout');
              
            }else{
              Swal.fire({
                icon: 'error',
                title: 'Please fill up the missing fields',
                showConfirmButton: false,
                timer: 3000
              })
            }
            
          }else{
            Swal.fire({
              icon: 'error',
              title: 'Saving Failed',
              text: 'The selected date is a holiday',
              showConfirmButton: false,
              timer: 4000
            })
          }
        });
      
     
      
      break;
      
      case 'btngroupdeactivate':
      
      if(rowdata.isUsed === '1'){return;}
      SAVE_DATA('group', {groupid:rowdata.groupid}, 'group_delete');
      TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).remove().draw();
      break;

      case 'btnholidaydeactivate':
        console.log(rowdata);
      SAVE_DATA('holiday', {holidayid:rowdata.holidayid,holidaydate:rowdata.holidaydate}, 'holiday_delete');
      TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).remove().draw();
      
      break;
      
      case 'btncreateschedule':

        REQUEST_CONTAINER = {action:'check_holiday',datedesc:$('#txtdate').val()};
        LOAD_DATA(cell,function(output){
          
          if(output.length === 0){

            REQUEST_CONTAINER = {
              action:'check_duplicate_entry',
              barcode:$('#lbbarcode').html(),
              groupid :$('#cmbgroup3').val(),
              scheduledate :$('#txtdate').val(),
              storeid :$('#cmbbranch3').val()
            };
    
            LOAD_DATA(cell,function(output){
    
              // console.log(output);
              // console.log(output.length);
                if(output.length === 0){
    
                  if($('#cmbgroup3').val()){
          
                    var parameter = {
                      scheduledate:$('#txtdate').val(),
                      productid:$('#txtproductid').val(),
                      groupid:$('#cmbgroup3').children("option:selected").val(),
                      barcode: $('#lbbarcode').html(),
                      storeid:$('#cmbbranch3').val()
                    };
            
                    if($('#txtitemtypeid').val() === '10'){
            
                      SAVE_DATA(module_name, parameter , 'item_add_schedule');
            
                    }else{
                      Swal.fire({
                        icon: 'error',
                        title: 'You can only add outright items only',
                        showConfirmButton: false,
                        timer: 3000
                      })
                    }
                    
                    // INSERT_REASON
                  }else{
                    Swal.fire({
                      icon: 'error',
                      title: 'There is no group selected',
                      showConfirmButton: false,
                      timer: 3000
                    })
                  }
    
                }else{
    
                  Swal.fire({
                    icon: 'error',
                    title: 'The product already exists in the schedule',
                    showConfirmButton: false,
                    timer: 3000
                  });
    
                }
            });

        
            
          }else{
            Swal.fire({
              icon: 'error',
              title: 'Saving Failed',
              text: 'The selected date is a holiday',
              showConfirmButton: false,
              timer: 4000
            })
          }
        });

      break;
      
      case 'btnledger':
      $('#lbdescription').html(rowdata.description); 
      $('#lbbarcode').html(rowdata.barcode);
      REQUEST_CONTAINER = { action: 'index_ledger', storeid: rowdata.storeid,barcode:rowdata.barcode};
      
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
      case 'open':
      break;
      
      case 'submitted':
      
      break;
      case 'approved':
      
      break;
      case 'nvlogout':
      USER_CONTROL('user_logout');
      return;
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
      $('#lnk'+PAGE_NAME).removeClass('active');
      LINK_ACTIVATE('#lnksysholiday');
      $('#HolidayModal').modal('show');
      return;
      case 'nvchangepassword':
      $('#ChangePassModal').modal('show');
      $('#txtfullname').val($('#nvusername').html());
      return;
      case 'lnksettings':
      var x = document.getElementById('chldsettings');
      if (x.style.display === "none") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
      }
      break;
      case 'lnkremovedlist':
      REQUEST_CONTAINER = { action: 'item_child', statusid: 3};
      CHILD_ITEM('#tbitemremoved');
      break;
      case 'lnkitems':
      // SEARCH_ITEM();
      break;
      case 'lnkaddeditems':
      REQUEST_CONTAINER = { action: 'item_child', statusid: 1};
      CHILD_ITEM('#tbaddeditem');
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
      // LOAD_INDEX();
      $("#lbfrom").show();
      $("#lbto").show();
      $("#dt_index_datefrom").show();
      $("#dt_index_dateto").show();
      break;
      case 'ledger':
      // REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6 };
      break;
      case 'currentcount':
      $("#lbfrom").hide();
      $("#lbto").hide();
      $("#dt_index_datefrom").hide();
      $("#dt_index_dateto").hide();
      
      // REQUEST_CONTAINER = { action: 'index_currentcount', statusid: 6,categoryname:CATEGORY_NAME,datefrom:document.getElementById("dt_index_datefrom").value,dateto:document.getElementById("dt_index_dateto").value,storeid:$('#cmbbranch').val(),groupid:$('#cmbgroup').val()};
      // COLUMN_CONTAINER = INDEX_COLUMNS;
      // COLUMN_DEFS = INDEX_COLUMNDEFS;
      // LOAD_DATATABLE('#tb' + PAGE_NAME);
      break;
    }
    
    switch(PAGE_NAME) {
      
      case 'index':
      if(this.id === 'lnkindex'){
        LOAD_INDEX();
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
        LOAD_DATA_IN_COMBOBOX('cmbdivision2', { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname');
        LOAD_DATA_IN_COMBOBOX('cmbbrand2',{ action: "brand", module_name: 'item',branchid: BRANCH_ID },"php/AdminController.php",'brandid','brandname');
        LOAD_DATA_IN_COMBOBOX('cmbbranch2',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
        $('#cmbbranch2').select2();
        $('#cmbgroup2').select2();
        $('#cmbdivision2').select2();
        $('#cmbbrand2').select2();
        
        var refreshId = setInterval(function() {
          if ($('#cmbbranch2').val() != 0) {
            $('#cmbbranch2').val(322).trigger("change");
            clearInterval(refreshId);
          }
        }, 500);
        
        
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
  
  function LOAD_INDEX(){
    
    REQUEST_CONTAINER = { action: PAGE_NAME, statusid: 6,groupid:$('#cmbgroup').val(),categoryname:CATEGORY_NAME,datefrom:document.getElementById("dt_index_datefrom").value,dateto:document.getElementById("dt_index_dateto").value,storeid:$('#cmbbranch').val()};
    COLUMN_CONTAINER = INDEX_COLUMNS;
    COLUMN_DEFS = INDEX_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME,false);
    
  }
  
  function LOAD_DATABASE(){
    
    REQUEST_CONTAINER = { action: PAGE_NAME};
    COLUMN_CONTAINER = DATABASE_COLUMNS;
    COLUMN_DEFS = DATABASE_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
    
  }
  
  function LOAD_ITEM(){
    
    REQUEST_CONTAINER = { action: PAGE_NAME, storeid:$('#cmbbranch').val(),groupid:$('#cmbgroup').val(),barcode:$('#fltbarcode').val(),description:$('#fltdescription').val(),divisionid:$('#cmbdivision').val(),brandid:$('#cmbbrand').val()};
    COLUMN_CONTAINER = ITEM_COLUMNS;
    COLUMN_DEFS = ITEM_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME);
    
  }
  
  function LOAD_INVENTORY(){
    
    var sid = 0;
    if($("#liopen").hasClass("active") === true){ sid = 1; }
    else if($("#lisubmitted").hasClass("active") === true){ sid = 2; }
    else if($("#liapproved").hasClass("active") === true){ sid = 6; }
    
    REQUEST_CONTAINER = { 
      action: PAGE_NAME, statusid: sid,datefrom:document.getElementById("dt_index_datefrom").value,
      dateto:document.getElementById("dt_index_dateto").value,storeid:$('#cmbbranch').val(),groupid:$('#cmbgroup').val()
    };
    COLUMN_CONTAINER = INVENTORY_COLUMNS;
    COLUMN_DEFS = INVENTORY_COLUMNDEFS;
    LOAD_DATATABLE('#tb' + PAGE_NAME,false);
    
  }
  
  function SEARCH_ITEM(){
    REQUEST_CONTAINER = { action: 'item_search', statusid: 1,datefrom: $('#dtfrom').val(),dateto: $('#dtto').val(),storeid:$('#cmbbranch2').val(),groupid:$('#cmbgroup2').val(),divisionid:$('#cmbdivision2').val(),brandid:$('#cmbbrand2').val(),barcode:$('#fltbarcode2').val(),legacybarcode:$('#fltlegacybarcode2').val(),description:$('#fltdescription2').val() };
    COLUMN_CONTAINER = ITEM_CANCEL_COLUMNS;
    COLUMN_DEFS = ITEM_CANCEL_COLUMNDEFS;
    LOAD_DATATABLE('#tbitemcancel',false);
  }
  
  function CHILD_ITEM(tablename){
    COLUMN_CONTAINER = ITEM_CHILD_COLUMNS;
    COLUMN_DEFS = ITEM_CHILD_COLUMNDEFS;
    LOAD_DATATABLE(tablename,false);
  }
  
  
  function INSERT_REASON(){
    var data = TBL_CONTAINER.rows( ['.even.selected', '.odd.selected'] ).data().toArray();
    SAVE_DATA(PAGE_NAME, data, 'item_reason',null,$('#txtreason').val());
    //i tried using callback but it doesnt work in the second time
    SEARCH_ITEM();
  }
  
  function LINK_ACTIVATE(elementId){
    var refreshId = setInterval(function() {
      if ($(elementId).length != 0) {
        $(elementId).addClass("active");  
        clearInterval(refreshId);
      }
    }, 1000);
  }
  
  function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
  }
  
  if ($('#tb' + PAGE_NAME).length != 0) {
    
    switch(PAGE_NAME) {
      case 'index':
      
      var dateToday = GET_DATE_NOW();
      document.getElementById("dt_index_datefrom").value = dateToday; 
      document.getElementById("dt_index_dateto").value = dateToday;
      LOAD_DATA_IN_COMBOBOX('cmbbranch', { action: "group_branch", module_name: 'group' }, 'php/AdminController.php', 'organization_id', 'organization_name');
      $('#cmbstatus').select2();
      $('#cmbgroup').select2();
      $('#cmbbranch').select2();
      LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');
      LINK_ACTIVATE('#lnkindex');
      break;
      case 'inventory':
      
      $("#lionhand").hide();
      $("#liposted").hide();
      LOAD_DATA_IN_COMBOBOX('cmbbranch', { action: "group_branch", module_name: 'group' }, 'php/AdminController.php', 'organization_id', 'organization_name');
      var dateToday = GET_DATE_NOW();
      document.getElementById("dt_index_datefrom").value = dateToday; 
      document.getElementById("dt_index_dateto").value = dateToday;
      $('#cmbbranch').select2();
      $('#cmbgroup').select2();
      LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');

      LINK_ACTIVATE('#lnkinventory');
      break;
      case 'group':
      LINK_ACTIVATE('#lnkgroup');
      break;
      case 'item':
      LOAD_DATA_IN_COMBOBOX('cmbdivision', { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname');
      // LOAD_DATA_IN_COMBOBOX('cmbsupplier',{ action: "item_supplier2", module_name: 'item',branchid: BRANCH_ID },"php/AdminController.php",'segment1','vendor_name');
      LOAD_DATA_IN_COMBOBOX('cmbbrand',{ action: "brand", module_name: 'item',branchid: BRANCH_ID },"php/AdminController.php",'brandid','brandname');
      LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');
      LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      $('#cmbbranch').select2();
      $('#cmbgroup').select2();
      $('#cmbdivision').select2();
      $('#cmbbrand').select2()

      var det =  addDays(new Date(),1);
      var today = det;
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      today = yyyy + '-' + mm + '-' + dd;
      
      document.getElementById("dtfrom").min = today; 
      document.getElementById("dtto").min = today; 
      document.getElementById("dtfrom").value = today; 
      document.getElementById("dtto").value = today;
      LINK_ACTIVATE('#lnkitem');
      $("#btncancel").hide();
      
      break;
      case 'user':
      LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      LOAD_DATA_IN_COMBOBOX('cmbdepartment',{ action: "user_department", module_name: 'user' },"php/AdminController.php",'departmentid','departmentname');
      $("#usermodalcontainer").load("sys_user.html");   
      $('#cmbbranch').select2();
      $('#cmbdepartment').select2();
      $('#cmbdivision').select2();
      LINK_ACTIVATE('#lnkuser');
      break;
      case 'productschedule':

      LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      // LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');
      LOAD_DATA_IN_COMBOBOX('cmbstatus', { action: "status" }, 'php/AdminController.php', 'statusid', 'statusname');
      $('#cmbbranch').select2();
      $('#cmbgroup').select2();
      $('#cmbstatus').select2();
      LINK_ACTIVATE('#lnkproductschedule');
      
      var chld = setInterval(function() {
        if($('#chldreport').val().length === 0){
          document.getElementById('chldreport').style.display = "block";
        };
        clearInterval(chld);
      }, 300);
      
      break;
      case 'scheduledgroup':
        LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
        // LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group',isused: null }, 'php/AdminController.php', 'groupid', 'groupname');
        LOAD_DATA_IN_COMBOBOX('cmbstatus', { action: "status" }, 'php/AdminController.php', 'statusid', 'statusname');
        $('#cmbbranch').select2();
        $('#cmbgroup').select2();
        $('#cmbstatus').select2();
        LINK_ACTIVATE('#lnkscheduledgroup');
        
        var chld = setInterval(function() {
          if($('#chldreport').val().length === 0){
            document.getElementById('chldreport').style.display = "block";
          };
          clearInterval(chld);
        }, 300);
        
        break;
      case 'database':
      LOAD_DATA_IN_COMBOBOX('cmbbranch',{ action: "user_branch", module_name: 'user'},"php/AdminController.php",'organization_id','organization_name');
      LOAD_DATABASE();
      $('#cmbbranch').select2();
      LINK_ACTIVATE('#lnkdatabase');

      // const restrictedId = ['516','124','696','718','675','655','998','1018'];
      // $("#cmbranch").each(function() {
      //     if(restrictedId.includes($(this).val())){
      //         $(this).remove();
      //     }
      // });

      //remove selection of stores using centralized version
      setTimeout(function () {
            $("#cmbbranch option[value='516']").remove();
            $("#cmbbranch option[value='124']").remove();
            $("#cmbbranch option[value='696']").remove();
            $("#cmbbranch option[value='718']").remove();
            $("#cmbbranch option[value='675']").remove();
            $("#cmbbranch option[value='655']").remove();
            $("#cmbbranch option[value='998']").remove();
            $("#cmbbranch option[value='1018']").remove();
      }, 3000);


      break; 
      default:
      // code block
    }
    
  }
  
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //            LOAD DATATABLE
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  
  function LOAD_DATATABLE(TABLE_NAME,ordering = true) {
    var col = [];
    if(PAGE_NAME === 'index'){
      
      col = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17];
      
    }else if(PAGE_NAME === 'inventory'){
      
      col = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19];
      
    }else if(PAGE_NAME === 'productschedule' || PAGE_NAME === 'scheduledgroup'){
      
      // col = [ 1, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19 ];
      col = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19 ];

    }
    
    TBL_CONTAINER = null;
    $(TABLE_NAME).dataTable().fnClearTable();
    $(TABLE_NAME).dataTable().fnDestroy();
    $.ajax({
      url: "php/AdminController.php",
      method: "POST",
      data: REQUEST_CONTAINER,
      dataType: "JSON",
      beforeSend: function() {
        document.getElementById('loader').style.display = "block";
      },
      complete: function() {
        document.getElementById('loader').style.display = "none";
      },
      success: function (data) {
        document.getElementById('loader').style.display = "none";
        
        if ($.fn.DataTable.isDataTable(TABLE_NAME) === false) {
          TBL_CONTAINER = $(TABLE_NAME).DataTable({
            "autoWidth": false,
            "data": data,
            "ordering": ordering,
            "columns": COLUMN_CONTAINER,
            "columnDefs": COLUMN_DEFS,
            // stateSave: true,
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
                  columns: col
                  
                },
                
                customize: function ( win ) {
                  $(win.document.body)
                  .css( 'font-size', '7pt' )
                  $(win.document.body).find( 'table' )
                  .addClass( 'compact' )
                  .css( 'font-size', '7pt' );
                  // console.log(win.document.body);
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
                  columns: col
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
            // order: [2, 'asc'],
            // "deferRender": true
          });
        } else {
          if (data) {
            TBL_CONTAINER.clear();
            TBL_CONTAINER.rows.add(data).draw();
          }
        }
        
        $(TABLE_NAME + ' tbody').off('click', 'input[type="checkbox"]').on('click', 'input[type="checkbox"]', function () {
          var row = $(this).parent().parent();
          
          // if(module_name != 'holiday' || module_name != 'group'){
          
          if ($(this).is(":checked")){$(row).toggleClass('selected');}
          else{ $(row).removeClass("selected"); }
          
          // }
          
          
        });
        
        TBL_CONTAINER.on("click", 'input[type="checkbox"]', function() {
          if ($(this).is(":checked")){
            TBL_CONTAINER.rows().select();
            var data = TBL_CONTAINER.rows( ['.even.selected', '.odd.selected'] ).data().toArray();
          }else{
            TBL_CONTAINER.rows().deselect();
          }
        });
        
        function _initKeyUpHandler($el){

        if(cellindex === 1){
          // console.log('date input shown!');
          $(TABLE_NAME).on('input', $el, function () {
            // console.log(module_name);
            var changed = 0;
            var dataContainer = TBL_CONTAINER.rows().data();
  
            dataContainer.each(function (value, index) {
              if(String($($el).val()) === String(value.holidaydate)){
                  changed = 1;
              } 
                // console.log(`For index ${index}, data value is ${value.groupname}`);
            });
  
            if(changed == 1){
              $(ROW_DATA_FOR_UPDATE).css("background-color", "Red");
            }else{
              $(ROW_DATA_FOR_UPDATE).css("background-color", "White");
            }
          });
        }
        
        if(cellindex === 0 && module_name === 'group'){
          $(TABLE_NAME).on('input', $el, function () {
            // console.log(module_name);
            var changed = 0;
            var dataContainer = TBL_CONTAINER.rows().data();
  
            dataContainer.each(function (value, index) {
              // console.log($($el).val() + '-----'+value.groupname);
              if(String($($el).val()) === String(value.groupname)){
                  changed = 1;
              } 
                // console.log(`For index ${index}, data value is ${value.groupname}`);
            });
  
            if(changed == 1){
              $(ROW_DATA_FOR_UPDATE).css("background-color", "Red");
            }else{
              $(ROW_DATA_FOR_UPDATE).css("background-color", "White");
            }
          });
        }

          var CURRENT_PAGE = $("#"+module_name+"_table").dataTable().api().page.info();
          $(TABLE_NAME).on("focusout", $el, function (e) {
            // console.log(cell);
            var tempdata; 
            var tempid;
            
            // console.log($($el).val());
            // console.log($el);
            
            if($($el).val() != ''){
              tempdata = originaldata;
              tempid = $($el).val();
              
            }else{
              tempdata = 'NOT SET';
              tempid = 0;
            }
            
            if(PAGE_NAME === 'assignment'){
              UPDATE = {
                user_id: rowdata.user_id,
                employee_name: rowdata.employee_name,
                employee_num: rowdata.employee_num,
                store_id: rowdata.store_id,
                store_name: rowdata.store_name,
                role_id: rowdata.role_id,
                role_name: rowdata.role_name,
                division_id: tempid,
                division_name: tempdata,
                selectedtatus: rowdata.status,
                assignmentid: rowdata.assignmentid
              };
              
              TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
              $($el).hide();
              
            }else{
              cell.data(originaldata).draw();
            }
            
            //cell.data(originaldata).draw();
            $("#"+module_name+"_table").dataTable().fnPageChange(parseInt(CURRENT_PAGE.page));
            TABLE_PARAMETER = TBL_CONTAINER.rows().data().toArray();
          });
          
        }
        

        $('#GroupModal').off('shown.bs.modal').on('shown.bs.modal', function () {

          TBL_CONTAINER.order([0, 'desc']).draw();
  
        });

        $('#HolidayModal').off('shown.bs.modal').on('shown.bs.modal', function () {

          TBL_CONTAINER.order([0, 'desc']).draw();
  
        });



        $('#addItemLegacy').off('shown.bs.modal').on('shown.bs.modal', function () {
          // console.log('shown data!');
          var refreshId = setInterval(function() {
            if ($('#cmbbranch4').val() != 0) {
              $('#cmbbranch4').val(322).trigger("change");
              clearInterval(refreshId);
            }
          }, 500);
          
        });
        
        $('#myModal').off('shown.bs.modal').on('shown.bs.modal', function () {
          
          var refreshId = setInterval(function() {
            if ($('#cmbbranch3').val() != 0) {
              $('#cmbbranch3').val(322).trigger("change");
              clearInterval(refreshId);
            }
          }, 500);
          
        });
        
        $('#UserModal').off('shown.bs.modal').on('shown.bs.modal', function () {

          if(rowdata.assigned_store != 'Main Office'){
            LOAD_DATA_IN_COMBOBOX('cmbdivision', { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname');
          }else{
            $('#cmbdivision').html('');
          }

          $('#cmbdivision').val(null).trigger('change');
          $('#txtuserdivisionid').val(rowdata.userdivisionid);
          // $('#txtempid').val('');

          console.log($('#txtuserdivisionid').val());
          
          REQUEST_CONTAINER = {action:'user_division',userdivisionid:$('#txtuserdivisionid').val()};
          // console.log(REQUEST_CONTAINER);
          LOAD_DATA(cell,function(output){
            
            if (!$.trim(output)){   
              
            }
            else{   
              
              var string = output[0].divisionid;
              var divisionid = string.split(',');
              console.log(divisionid);
              
              $('#cmbdivision').val(divisionid);
              $('#txtuserdivisionid').val(output[0].userdivisionid);
              $('#txtusername').val(output[0].username);
              $('#txtpassword').val(output[0].password);

              // console.log(output[0].statusid);

              if(output[0].statusid === '4'){
                  $('#cbstatus').prop('checked', true);
                  console.log('active!');
              }else{
                  $('#cbstatus').prop('checked', false);
                     console.log('inactive!');
              }
              
              var ctr = 0;
              $.each($("#cmbdivision"), function(){
                // console.log(ctr);
                ctr++;
                $(this).append('val', divisionid).trigger('change');
              });
            }
          });
        });  
        



        $('#UserModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {

          UPDATE =
          {
            "employee_number": rowdata.employee_number,
            "full_name": rowdata.full_name,
            "job_description": rowdata.job_description,
            "assigned_store": rowdata.assigned_store,
            "divisionname": selected_division.join(),
            "store_id": rowdata.store_id,
            "departments": rowdata.departments,
            "job_code":rowdata.job_code,
            "userdivisionid":$('#txtuserdivisionid').val()
          };
          TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();

        });


        $('#GroupModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {

          var filteredData =  TABLE_PARAMETER.filter(function(loopData) {
            return loopData.isUsed === '0';
          });
          
          SAVE_DATA(module_name, filteredData, 'manage_settings');

          COLUMN_CONTAINER = SYS_GROUP_COLUMNS;
          COLUMN_DEFS = GROUP_COLUMNDEFS;
          REQUEST_CONTAINER = GROUP_PARAMETER;

          setTimeout(function(){

            LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings_v2", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');

          }, 2000);

          // LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings_v2", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');
          // LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');
          
        });
        
        $('#HolidayModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
          SAVE_DATA(module_name, TABLE_PARAMETER, 'manage_settings');
          COLUMN_CONTAINER = HOLIDAY_COLUMNS;
          COLUMN_DEFS = HOLIDAY_COLUMNDEFS;
          REQUEST_CONTAINER = HOLIDAY_PARAMETER;
          $('#lnksysholiday').removeClass('active');
          LINK_ACTIVATE('#lnk'+PAGE_NAME);
        });

        $(TABLE_NAME).on('change', 'input[type="text"]', function () {
          originaldata = this.value;
        });
        
        $(TABLE_NAME).on('change', 'input[type="date"]', function () {
          originaldata = this.value;
        });
        
        $(TABLE_NAME).on('focus', 'input[type="text"]', function () {
          originaldata = this.value;
        });
        
        $(TABLE_NAME).on('focus', 'input[type="date"]', function () {
          originaldata = this.value;
        });
        
        // ROW CLICK
        
        $(TABLE_NAME + ' tbody').off('click', 'tr').on('click', 'tr', function () {
          
          rowdata = TBL_CONTAINER.row(this).data();
          
          ROW_DATA_FOR_UPDATE = this;
          var CURRENT_PAGE = $("#"+module_name+"_table").dataTable().api().page.info();
          
          
          if(module_name === 'group'){
            $(this).removeClass("selected");
          }
          if(module_name === 'holiday'){
            $(this).removeClass("selected"); 
          }
          
          if(TABLE_NAME === '#tbinventory' || TABLE_NAME === '#group_table' || TABLE_NAME === '#holiday_table' || TABLE_NAME === '#tbassignment'){
            
            
            // cell click fires twice idont know why thats why attached filter hire if the select elements exists
            // prevents the re drawing the element again
            var elementexists = celldata.includes('select');
            
            if (elementexists === true) {
              return;
            } 
            
            if(cellindex === 3 && TABLE_NAME === '#holiday_table'){
              return;
            }
            
            if(cellindex === 2 && TABLE_NAME === '#group_table'){
              return;
            }
            
            var _drawCell = function(cell,element){
              
              if(PAGE_NAME != 'assignment'){
                var elementexists = celldata.includes('input');
                
              }else{
                var elementexists = celldata.includes('select');
              }
              
              if (elementexists === false) {
                
                cell.data(element).draw();
                $("#"+module_name+"_table").dataTable().fnPageChange(parseInt(CURRENT_PAGE.page));
                
                
                document.getElementById($(element).attr('id')).focus();
                
                if(PAGE_NAME === 'assignment'){
                  
                  LOAD_DATA_IN_COMBOBOX('cmbdivision'+ctr, { action: "division", module_name: PAGE_NAME }, 'php/AdminController.php', 'divisionid', 'divisionname',true);
                  setTimeout(function(){  
                    // $('#cmbdivision'+ctr).val(rowdata.division_id);
                    $('#cmbdivision'+ctr).val(rowdata.division_id);
                    // console.log($('#cmbdivision'+ctr).val());
                  }, 2000);
                  
                }
                ctr = ctr + 1;
              } 
            }
            
            switch (TABLE_NAME) {
              case '#group_table':
              if (cellindex === 0) {
                if(rowdata.isUsed === '1'){return;}
                var element = '<input type = "text" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                _initKeyUpHandler("#txttemp" + ctr); 
                _drawCell(cell,element);
              } 
              break;
              case '#holiday_table':
              if (cellindex === 0) {
                var element = '<input type = "text" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                _initKeyUpHandler("#txttemp" + ctr); 
                _drawCell(cell,element);
              } else if (cellindex === 1) {

                 if(rowdata.holidayid === "999999"){
                
                var element = '<input type = "date" class = "temp" id = "txttemp'+ctr+'" value = "' + celldata + '" width = "50px" autofocus>';
                _initKeyUpHandler("#txttemp" + ctr); 
                _drawCell(cell,element);
              }

              }
              
              break;
              
              default:
            }
          }
        });
        
        //CELL CLICK
        
        $(TABLE_NAME + ' tbody').off('click', 'td').on('click', 'td', function () {
          
          xrow = TBL_CONTAINER.row($(this).closest('tr')).data();  
          cellindex = TBL_CONTAINER.cell(this).index().columnVisible;
          cell = TBL_CONTAINER.cell(this);
          celldata = TBL_CONTAINER.cell(this).data();
          
        });
        
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

function SAVE_DATA(module_name, parameter, action, callback = null,extraParameter = null) {


  
  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    
    data: { action: action, table_data: parameter, module_name: module_name,extra:extraParameter },
    dataType: "JSON",
    
    beforeSend: function() {
      
      document.getElementById('loader').style.display = "block";
      
    },
    success: function (data) {
      document.getElementById('loader').style.display = "none";
      var UPDATE = null;
      
      if(action === 'database_update'){
        callback();
      }
      
      if(action === 'item_cancel'){
        $('#cancelModal').modal('hide');
        callback();
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'item successfully cancelled',
          showConfirmButton: false,
          timer: 3000
        })
      }
      
      if (action === 'item_add_schedule'){
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'item successfully added',
          showConfirmButton: false,
          timer: 3000
        })

        $('#myModal').modal('hide');
      }
      
      if (action === 'item_add_schedule_phaseout'){
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'item successfully added',
          showConfirmButton: false,
          timer: 3000
        })
      }
      
      if(action === 'item_cancel_row'){
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'item/s cancelled',
          showConfirmButton: false,
          timer: 3000
        })
      }
      
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
        });
        
        TBL_CONTAINER.clear().draw();
      }
      
      if(PAGE_NAME === 'database'){
        
        Swal.fire({
          // position: 'top-end',
          icon: 'success',
          title: 'Successfully updated',
          showConfirmButton: false,
          timer: 1500
        });
        
      }

      if(action === 'change_pass'){

        if (data.status === 1){
          Swal.fire({
            // position: 'top-end',
            icon: 'success',
            title: 'successfully changed password!',
            showConfirmButton: false,
            timer: 1500
          });
        }
        else{
           Swal.fire({
            icon: 'error',
            title: 'The old password doesnt match current password',
            showConfirmButton: false,
            timer: 1500
          });
        }

      }
      
      if(PAGE_NAME === 'user'){

        var tempId = '0';

        if($('#txtuserdivisionid').val() === '0'){
          tempId = data.id;
          // console.log('insert');
          $('#txtuserdivisionid').val(data.id);
        }
        
        if(data.success === 'true'){
          
          Swal.fire({
            // position: 'top-end',
            icon: 'success',
            title: data.requeststatus,
            showConfirmButton: false,
            timer: 1500
          });
        }else{
          
          Swal.fire({
            icon: 'error',
            title: data.requeststatus,
            showConfirmButton: false,
            timer: 1500
          });
          
        }

      }else if (PAGE_NAME === 'login'){
        console.log(data.roleid);
        if (data.requeststatus === 1){
          if(data.roleid != '4'){
            window.location.replace('inventory.html');
          }
          else if (data.roleid === '4'){
            // console.log(PAGE_NAME);
            window.location.replace('index.html');
          }
        }else{
          Swal.fire({
            // position: 'top-end',
            icon: 'error',
            title: 'Wrong credentials or your account is restricted to access this page ',
            showConfirmButton: false,
            timer: 1500
          })
          
        }
      }
      
      if(UPDATE != null){
        // TBL_CONTAINER.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();
      }
      
      document.getElementById('loader').style.display = "none";
      
    },
    complete: function (data) {
      document.getElementById('loader').style.display = "none";
      
    },
    error: function (data) {
      console.log(data);
      document.getElementById('loader').style.display = "none";
// 
let msg = 'Cant connect to '+$("#cmbbranch option:selected").html()+' Server'
       if(PAGE_NAME === 'database'){
        
        Swal.fire({
          // position: 'top-end',
          icon: 'error',
          text: 'Download Failed',
          title: msg,
          showConfirmButton: false,
            showCloseButton: true
        });
        
      }

      if(PAGE_NAME === 'user'){
        
        Swal.fire({
          // position: 'top-end',
          icon: 'error',
          text: data.requeststatus,
          title: msg,
          showConfirmButton: false,
          showCloseButton: true
        });
        
      }

      
    },
  });
}

function ADD_NEW_ROW() {
  if (module_name === "group") {

    // UPDATE =
    // {
    //   "groupid": "0",
    //   "groupname": "",
    //   "statusname": "Open",
    //   "recordstatus": "4",
    // };

    TBL_CONTAINER.row.add({
      "groupid": "999999",
      "groupname": "",
      "statusname": "Open",
      "recordstatus": "4",
      "isUsed": "0",
    }).draw();
  }
  else if (module_name === "holiday") {
    TBL_CONTAINER.row.add({
      "holidayid": "999999",
      "holidayname": "",
      "holidaydate": "",
      "statusname": "Active",
      "recordstatus": "4",
    }).draw();
  }

  TBL_CONTAINER.order([0, 'desc']).draw();


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
      
      if(PAGE_NAME === 'user' && element === 'cmbbranch'){
        $('#cmbbranch').prepend("<option value='-1' selected='selected'>MAIN OFFICE</option>");
      }
      
      if(PAGE_NAME === 'productschedule' && element === 'cmbstatus'){
        $('#cmbstatus').prepend("<option value='-1' selected='selected'>All</option>");
      }

      if(PAGE_NAME === 'productschedule' && element === 'cmbgroup'){
        $('#cmbgroup').prepend("<option value='-1' selected='selected'>All</option>");
      }
      
      // if(PAGE_NAME === 'scheduledgroup' && element === 'cmbgroup'){
      //   $('#cmbgroup').prepend("<option value='-1' selected='selected'>All</option>");
      // }

      if(PAGE_NAME === 'item' && element === 'cmbdivision'){
        $('#cmbdivision').prepend("<option value='-1' selected='selected'>All</option>");
      }
      
      if(PAGE_NAME === 'item' && element === 'cmbdivision2'){
        $('#cmbdivision2').prepend("<option value='-1' selected='selected'>All</option>");
      }
      
      if(PAGE_NAME === 'item' && element === 'cmbbrand'){
        $('#cmbbrand').prepend("<option value='-1' selected='selected'>All</option>");
      }
      
      if(PAGE_NAME === 'item' && element === 'cmbbrand2'){
        $('#cmbbrand2').prepend("<option value='-1' selected='selected'>All</option>");
        
      }
      
      if(PAGE_NAME === 'scheduledgroup' && element === 'cmbstatus'){
        $('#cmbstatus').prepend("<option value='-1' selected='selected'>All</option>");
      }
      
      if(PAGE_NAME === 'item' && element === 'cmbgroup'){
        $('#cmbgroup').prepend("<option value='-1' selected='selected'>All</option>");
      }

      if(PAGE_NAME === 'index' && element === 'cmbgroup'){
        $('#cmbgroup').prepend("<option value='-1' selected='selected'>All</option>");
      }

      if(PAGE_NAME === 'inventory' && element === 'cmbgroup'){
        $('#cmbgroup').prepend("<option value='-1' selected='selected'>All</option>");
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
  
  DEPARTMENT = data.department;
  
  var checkExist = setInterval(function() {
    
    if ($(window).length) {
      
      $('#txtbranchid').val(data.branchid);
      $('#nvusername').text(data.personnelname);
      $('#nvbranch').text(data.departmentname);
      $('#txtcategoryid').val(data.categoryid);
      $('#txtroleid').val(data.roleid);
      
      if(DEPARTMENT === 'Internal Audit Group'){
        
        $("#lnkuser").hide();
        $("#lnksettings").hide();
        $("#lnkgroup").hide();
        $("#lnkdatabase").hide();
        // $("#lnkinventory").hide();
        $("#lnkitem").hide();
        // $("#lnkindex").hide();
        $("#lnksysholiday").hide();
        
      }
      else if (DEPARTMENT === 'Information Technology'){
        
        $("#lnkuser").hide();
        $("#lnksettings").hide();
        $("#lnkgroup").hide();
        $("#lnkdatabase").hide();
        $("#lnkitem").hide();
        $("#lnksysholiday").hide();

      }
      
      clearInterval(checkExist);
    }
  }, 200); // check every 100ms
  
}

function USER_CONTROL(action,callback){
  
  $.ajax({
    
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
      console.log(data);
      window.location.replace('login.html');
    },
  });
}

$(document).on("click", "#btnlogin", function () {
  // PAGE_NAME = 'login';
  SAVE_DATA(PAGE_NAME,{username: $('#txtusername').val(),password: $('#txtpassword').val()},'user_authenticate');
});

$(document).on('change', "#cmbcategory", function () {
  if(PAGE_NAME === 'item'){
    var val = $(this).val();
    TBL_CONTAINER.column(5).search(val ? '^' + val + '$' : '', true, false).draw();
  }
});

$(document).on('change', "#cmbbranch4", function () {
  if(PAGE_NAME === 'item'){
    var val = $(this).val();
    LOAD_DATA_IN_COMBOBOX('cmbgroup4', { action: "active_group",paramdate: $('#txtdate2').val(), module_name: 'group',isused: null,storeid:$('#cmbbranch4').val() }, 'php/AdminController.php', 'groupid', 'groupname');
  }
});

$(document).on('change', "#cmbbranch3", function () {
  if(PAGE_NAME === 'item'){
    var val = $(this).val();
    LOAD_DATA_IN_COMBOBOX('cmbgroup3', { action: "active_group",paramdate: $('#txtdate').val(), module_name: 'group',isused: null,storeid:$('#cmbbranch3').val() }, 'php/AdminController.php', 'groupid', 'groupname');
  }
});

$(document).on('change', "#cmbbranch2", function () {
  if(PAGE_NAME === 'item'){
    var val = $(this).val();
    LOAD_DATA_IN_COMBOBOX('cmbgroup2', { action: "item_branch_group", module_name: 'group',isused: null,storeid:$('#cmbbranch2').val() }, 'php/AdminController.php', 'groupid', 'groupname');
  }
});   

$(document).on('change', "#txtdate", function () {

  if(PAGE_NAME === 'item'){

    LOAD_DATA_IN_COMBOBOX('cmbgroup3', { action: "active_group",paramdate: $('#txtdate').val(), module_name: 'group',isused: null,storeid:$('#cmbbranch3').val() }, 'php/AdminController.php', 'groupid', 'groupname');

  }
});

$(document).on('change', "#txtdate2", function () {

  if(PAGE_NAME === 'item'){

    LOAD_DATA_IN_COMBOBOX('cmbgroup4', { action: "active_group",paramdate: $('#txtdate2').val(), module_name: 'group',isused: null,storeid:$('#cmbbranch4').val() }, 'php/AdminController.php', 'groupid', 'groupname');

  }
});

$(document).on('change', "#cmbbranch", function () {
  if(PAGE_NAME === 'productschedule'){
    // console.log('fire!');
    var val = $(this).val();
    LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "item_branch_group", module_name: 'group',isused: null,storeid:$('#cmbbranch').val() }, 'php/AdminController.php', 'groupid', 'groupname');
  }else if(PAGE_NAME === 'scheduledgroup'){
    var val = $(this).val();
    LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "item_branch_group", module_name: 'group',isused: null,storeid:$('#cmbbranch').val() }, 'php/AdminController.php', 'groupid', 'groupname');
  }
});

$(document).on('change', "#cmbbranch", function () {
  if($('#cmbbranch').val() === '-1'){
    $("#cmbdepartment").prop('disabled', false);
    $('#cmbdepartment').val(60666).trigger("change");
  }else{
    $("#cmbdepartment").prop('disabled', true);
    $('#cmbdepartment').val(60680).trigger("change");
  }
  
});

$(document).on('change', "#cmbsupplier", function () {
  var val = $(this).val();
  // TBL_CONTAINER.column(3).search(val ? '^' + val + '$' : '', true, false).draw();
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
      
      // console.log(data);
      var row = '';
      for(var i = 0; i < data.length; i++) {
        var obj = data[i];
        // console.log(Number(obj.onhand));
        // row +=  "<tr><td>"+obj.scheduledate+"</td><td>"+Number(obj.onhand)+"</td><td>"+Number(obj.sellout)+"</td><td>"+Number(obj.receiving)+"</td><td>"+Number(obj.rcv_ir)+"</td><td>"+Number(obj.rcv_er)+"</td><td>"+Number(obj.adj_issue)+"</td><td>"+Number(obj.adj_receipt)+"</td><td>"+Number(obj.sellingarea)+"</td><td>"+Number(obj.warehousearea)+"</td><td>"+Number(obj.variance)+"</td><td>"+obj.remarks+"</td></tr>";
        row +=  "<tr><td>"+obj.scheduledate+"</td><td align = 'right'>"+obj.onhand+"</td><td align = 'right'>"+obj.sellout+"</td><td align = 'right'>"+obj.receiving+"</td><td align = 'right'>"+obj.rcv_ir+"</td><td align = 'right'>"+obj.rcv_er+"</td><td align = 'right'>"+obj.adj_issue+"</td><td align = 'right'>"+obj.adj_receipt+"</td><td align = 'right'>"+obj.sellingarea+"</td><td align = 'right'>"+obj.warehousearea+"</td><td align = 'right'>"+obj.variance+"</td><td>"+obj.remarks+"</td></tr>";
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
