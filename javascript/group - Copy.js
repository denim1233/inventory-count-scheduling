var ROW_DATA;
var alib;
var GROUP_ID;
var BATCH_ID;
var CHILD_ID;
var tablechild = '#tbchild';
var ctr = 0;
var DT_CHILD;
var DT_CATEGORY;
var ROW_DATA_FOR_UPDATE;

$(document).ready(function () {

  $(document).on("click", "#btnbatchcancel", function () {
    if (ROW_DATA[0].statusname != 'Cancelled') {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.value) {
          CANCEL_ITEM(ROW_DATA[0].batchid);
        }
      })
    }

  });

  $("#ModalContainer1").load("grp_CancelModal.html");
  BATCH_ID = 0;
  GROUP_ID = 0;
  LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');
  LOAD_DATA_IN_COMBOBOX('cmbbranch', { action: "group_branch", module_name: 'group' }, 'php/AdminController.php', 'organization_id', 'organization_name');
  SCHEDULE_LOAD();
  LOAD_BRANCH();

  $(document).on("click", "a", function () {
    var divid = '#' + $(this).data("child");
    if ($(divid).is(':visible')) {
      $(divid).hide();
    } else {
      $(divid).show();
    }
  });

  $('a').click(function () {
    console.log('sidebar click!');

  });

  $("#btnupload").click(function () {

       UPLOAD_DATA();
       
  });

  $("#btnset").click(function () {

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Set'
    }).then((result) => {
      if (result.value) {
        if (BATCH_ID != 0) {
          SET_GROUP();
        } else {
          console.log('Please upload a file before setting a group')
        }
      }
    })
  });
});

function CANCEL_ITEM(pbatchid) {

  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: { action: 'group_cancel_batch', pbatchid: pbatchid },
    dataType: "JSON",
    success: function (data) {
      console.log(data);
    },
    complete: function (data) {
      Swal.fire(
        'Batch Cancelled!',
        '',
        'success'
      )

        UPDATE =
        {
          "batchid": ROW_DATA[0].batchid,
          "groupname": ROW_DATA[0].groupname,
          "countperyear": ROW_DATA[0].countperyear,
          "batchdate": ROW_DATA[0].groupname,
          "countperyear": ROW_DATA[0].countperyear,
          "storename": ROW_DATA[0].storename,
          "batchname": ROW_DATA[0].batchname,
          "statusname": 'Cancelled',
          "statusid": ROW_DATA[0].statusid
        };

        DT_CATEGORY.row(ROW_DATA_FOR_UPDATE).data(UPDATE).draw();

    },
    error: function (data) {
      console.log(data);
    },
  });

}

function SET_GROUP() {

  var pcountperyear = document.getElementById("cmbcountperyear").value;
  var GROUP_ID = document.getElementById("cmbgroup").value;

  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: { action: 'set_group_data', pbatchid: BATCH_ID, pgroupid: GROUP_ID, pcountperyear: pcountperyear },
    dataType: "JSON",
    success: function (data) {
      // console.log(data);
    },
    complete: function (data) {
      Swal.fire(
        'Group setted!',
        'The group of the file has been setted.',
        'success'
      )
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function LOAD_BRANCH() {
  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: { action: 'group_branch' },
    dataType: "JSON",
    beforeSend: function () {
      document.getElementById("loader").style.display = "block";
    },
    complete: function () {
      document.getElementById("loader").style.display = "none";
    },
    success: function (data) {
      // console.log(data);
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function UPLOAD_DATA() {
  // get the parameters heren;
  UPLOAD_PICTUREPATH = '';
  var filename = GET_FILENAME('btnbrowse');
  var p2 = filename;
  var datenow = CONVERT_TO_DATE('', 'mm/dd/yyyy');

  UPLOAD_PICTUREPATH = 'files/' + p2;
  var f;
  var fvar = new FormData();
  var pgroupid = document.getElementById("cmbgroup").value || 0;
  var pstoreid = document.getElementById("cmbbranch").value || 0;
  var pstartdate = document.getElementById("txtgrpdate").value;
  var pstorename = $("#cmbbranch option:selected").html();
  var pcountperyear = document.getElementById("cmbcountperyear").value;
  f = document.getElementById('btnbrowse').files[0];

  if(parseInt(pgroupid) === 0){
      Swal.fire({
          // position: 'top-end',
          icon: 'error',
          title: 'Saving Failed',
          text: 'Please Select a group',
          showConfirmButton: false,
          timer: 2000
        })

      return;

  }


  fvar.append('pbatchname', 'CT-00001');
  fvar.append('pbatchdate', datenow);
  fvar.append('precordstatus', 1);
  fvar.append('puser', 1);
  fvar.append('pgroupid', pgroupid);
  fvar.append('pstoreid', pstoreid);
  fvar.append('pstorename', pstorename);
  fvar.append('pfilereference', UPLOAD_PICTUREPATH);
  fvar.append('pcountperyear', pcountperyear);
  fvar.append('pstartdate', pstartdate);
  fvar.append('file', f);
  fvar.append('action', 'uploadfile');

  $.ajax({
    method: "POST",
    data: fvar,
    dataType: "JSON",
    processData: false,
    cache: false,
    contentType: false,
    url: "php/AdminController.php",
    beforeSend: function () {
      document.getElementById("loader").style.display = "block";
      $("#btnupload").attr("disabled", true);
    },
    complete: function (data) {
      Swal.fire(
        'Data uploaded!',
        'Your file has been uploaded.',
        'success'
      )
      SCHEDULE_LOAD();
      document.getElementById("loader").style.display = "none";
      $("#btnupload").attr("disabled", false);
      LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');

    },
    success: function (data) {
      console.log('upload success!');
      BATCH_ID = data['batchid'];
      $("#btnupload").attr("disabled", false);
      LOAD_DATA_IN_COMBOBOX('cmbgroup', { action: "load_group_settings", module_name: 'group', isused: 0 }, 'php/AdminController.php', 'groupid', 'groupname');

    },

    error: function (data) {
      console.log('uploading data error!');
      console.log(data);
      document.getElementById("loader").style.display = "none";
      $("#btnupload").attr("disabled", false);
    },
  });
}

function LOAD_CHILD(batchid) {
  if (batchid == null) {
    batchid = 0;
  }



  $('#tbchild').dataTable().fnClearTable();
  $('#tbchild').dataTable().fnDestroy();

  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: { action: 'groupchild', pbatchid: batchid },
    dataType: "JSON",
    "deferRender": true,
    success: function (data) {
      DT_CHILD = $('#tbchild').DataTable({
        "autoWidth": false,
        "data": data,
        "columns": [
          { "data": "barcode" },
          { "data": "scheduledate" },
          { "data": "groupname" },
          { "data": "onhand" },
          { "data": "sellout" },
          { "data": "receipts" },
          { "data": "adjustment" },
          { "data": "sellingarea" },
          { "data": "warehousearea" },
          { "data": "variance" },
          { "data": "remarks" },
        ],

        "columnDefs": [
          { "width": "80", "targets": 1 },
          { "width": "70px", "targets": 2 },
          { "width": "200px", "targets": 10 },
          { "width": "80px", "targets": 5 },
          { targets: 3, className: 'dt-body-right' },
          { targets: 4, className: 'dt-body-right' },
          { targets: 5, className: 'dt-body-right' },
          { targets: 6, className: 'dt-body-right' },
          { targets: 7, className: 'dt-body-right' },
          { targets: 8, className: 'dt-body-right' },
          { targets: 9, className: 'dt-body-right' }

        ],

        order: [[1, 'asc']]
      });

      $('#tbchild tbody').on('click', 'td.details-control', function () {



      });
    },
    error: function (data) {
      console.log('error on LOAD_CHILD function!');
      console.log(data);
    }
  });

}

function format(d) {

  return '<table id = "tbchild" class="table table-bordered">' +
    '<thead>' +
    '<tr>' +
    '<th>Barcode</th>' +
    '<th>Date</th>' +
    '<th>Group</th>' +
    '<th>Sys on Hand</th>' +
    '<th>Sell out</th>' +
    '<th>Receiving (Shipcon)</th>' +
    '<th>Inv Adj</th>' +
    '<th>Selling Area</th>' +
    '<th>Whouse Area</th>' +
    '<th>Variance</th>' +
    '<th>Rmrks</th>' +
    '</tr>' +
    '</thead>' +
    '<tbody id = "b1">' +
    '</tbody>' +
    '</table>';
}


function SCHEDULE_LOAD() {
  $.ajax({
    url: "php/AdminController.php",
    method: "POST",
    data: { action: 'group' },
    dataType: "JSON",

    success: function (data) {
      // console.log(data);

      if (DT_CATEGORY == null) {

        DT_CATEGORY = $('#tbgroup').DataTable({
          "autoWidth": false,
          "data": data,
          "columns": [
            {
              'className': 'details-control',
              'orderable': false,
              'data': null,
              'defaultContent': ''
            },
            { "data": "batchid" },
            { "data": "groupname" },
            { "data": "countperyear" },
            { "data": "batchdate" },
            { "data": "storename" },
            { "data": "batchname" },
            { "data": "statusname" },
            { "data": null },

          ],

          "columnDefs": [
            { "targets": [1], "visible": false, "searchable": false },
            { "width": "250px", "targets": 2 },
            { "width": "50px", "targets": 3 },
            { "width": "100px", "targets": 4 },
            { "width": "100px", "targets": 7 },
            { targets: 3, className: 'dt-body-right' },
            {
              targets: 8,
              "width": "100px",
              data: null,
              render: function (data, type, row) {
                var btncancel;
                if(data.statusname === 'Cancelled'){
                   btncancel = "<button type='button' id = 'btnbatchcancel' class='btn btn-info' disabled>Cancel</button>";
                }else{
                   btncancel = "<button type='button' id = 'btnbatchcancel' class='btn btn-info'>Cancel</button>";
                }
                return retdata = btncancel;
              }
            },
          ],

          order: [[1, 'asc']]
        });

      }
      else {
        DT_CATEGORY.clear();
        DT_CATEGORY.rows.add(data);
        DT_CATEGORY.draw();
      }

      $('#tbgroup tbody').off('click', 'td.details-control').on('click', 'td.details-control', function () {

        var tr = $(this).closest('tr');
        var row = DT_CATEGORY.row(tr);

        if (row.child.isShown()) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');
        } else {
          // Open this row
          // console.log(tr);
          $('#tbchild').dataTable().fnClearTable();
          $('#tbchild').dataTable().fnDestroy();
          $("#tbchild").remove();
          row.child(format()).show();
          tr.addClass('shown');
        }
      });

      $('#tbgroup tbody').on('click', 'button', function () {
        $('#myModal').modal('show');
      });

      $('#tbgroup tbody').off('click', 'tr').on('click', 'tr', function () {
        ROW_DATA_FOR_UPDATE = this;
        ROW_DATA = DT_CATEGORY.rows(this).data();
        console.log(ROW_DATA);
        CHILD_ID = ROW_DATA[0].batchid;
        LOAD_CHILD(CHILD_ID);

        // console.log(CHILD_ID);
      });

      $('#btn-show-all-children').on('click', function () {
        // Enumerate all rows
        table.rows().every(function () {
          // If row has details collapsed
          if (!this.child.isShown()) {
            // Open this row
            this.child(format(this.data())).show();
            $(this.node()).addClass('shown');
          }
        });
      });

      // Handle click on "Collapse All" button
      $('#btn-hide-all-children').on('click', function () {
        // Enumerate all rows
        table.rows().every(function () {
          // If row has details expanded
          if (this.child.isShown()) {
            // Collapse row details
            this.child.hide();
            $(this.node()).removeClass('shown');
          }
        });
      });
    },
    error: function (data) {
      console.log(data);
      // alert('error on SCHEDULE_LOAD!');
    }
  });
}

function GET_FILENAME(element) {
  var fullPath = document.getElementById(element).value;
  if (fullPath) {
    var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
    var filename = fullPath.substring(startIndex);
    if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
      filename = filename.substring(1);
    }
    //alert(filename);
    return filename;
  }
}

function CONVERT_TO_DATE(value, format) {
  value = new Date();
  var dd = value.getDate();
  var mm = value.getMonth() + 1; //January is 0!
  var yyyy = value.getFullYear();

  if (dd < 10) {
    dd = '0' + dd;
  }
  if (mm < 10) {
    mm = '0' + mm;
  }

  if (format === 'mm/dd/yyyy') {
    value = mm + '/' + dd + '/' + yyyy;
  }
  else if (format === 'dd/mm/yyyy') {
    value = dd + '/' + mm + '/' + yyyy;
  }
  else if (format === 'mm-dd-yyyy') {
    value = mm + '-' + dd + '-' + yyyy;
  }
  else if (format === 'dd-mm-yyyy') {
    value = dd + '-' + mm + '-' + yyyy;
  }

  //alert(value);
  return value;
}


function LOAD_DATA_IN_COMBOBOX(element, parameter, phpfile, col1, col2) {
  $.ajax({
    url: phpfile,
    method: "POST",
    data: parameter,
    dataType: "JSON",
    success: function (data) {
      console.log(data);
      $.ctr = 0;
      var select = document.getElementById(element);

      select.innerHTML = "";
      while ($.ctr < data.length) {
        var option = document.createElement('option');
        option.value = data[$.ctr][col1];
        option.text = data[$.ctr][col2];
        select.add(option, $.ctr);
        $.ctr = $.ctr + 1;
      }
    },
    error: function (data) {
      console.log('LOAD_DATA_IN_COMBOBOX -- error');
    },
  });

}