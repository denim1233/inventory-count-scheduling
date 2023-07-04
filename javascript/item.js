var CATEGORYID;
var ROW_DATA;
var alib;

$(document).ready(function(){
    $("#sidebar").load("sidebar.html"); 
    $("#appnavbar").load("navbar.html");
    
    CATEGORY_LOAD();
    SCHEDULE_LOAD();
    
    $(document).on("click", "a" , function() {
        var divid = '#'+$(this).data("child");
        if ( $(divid).is(':visible') ){
            $(divid).hide();
        }else{
            $(divid).show();
        }
    });
    
    // $("#btnadd").click(function() { CLEAR_DATA(); });
    // console.log('ambot');
});

$(document).on("click", "a" , function() {
    var divid = '#'+$(this).data("child");
    if ( $(divid).is(':visible') ){
        $(divid).hide();
    }else{
        $(divid).show();
    }
});

function CLEAR_DATA()
{
    $('#myModal').modal('show');
}

function CATEGORY_MANAGERECORD()
{
    var p1 = document.getElementById("txtcategory").value;
    $.ajax({
            url:"php/AdminController.php",
            method:"POST",
            data :{action:'item'},
            dataType: "JSON",
            success:function(data)
            {
                console.table(data);
                if(data[0]['statusid'] == '1')
                {
                    if(CATEGORYID === 0)
                    {
                         var INSERT = 
                        {
                        "CategoryId" : data[0]['pcategoryid'],
                        "CategoryName" : p1
                        };
                        DT_CATEGORY.row.add(INSERT).draw();
     
                       alert('data saved!');
                    }
                    else
                    {
                        var UPDATE = 
                        {
                        "CategoryId" : CATEGORYID,
                        "CategoryName" : p1
                        };
                        
                        DT_CATEGORY.row(ROW_DATA).data(UPDATE).draw(); 
                        alert('data updated!');
                    }
                }
                else
                {
                    alert(data[0]['querystatus']);
                }
                 
            },
            error: function(data) 
            {
                data[0]['ERRORMESSAGE'];
            },
    })
}
   
function CATEGORY_LOAD()
{
    $.ajax({
      url:"php/category/SYS_Category_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_CATEGORY = $('#tbitem').DataTable ({
             "autoWidth": false,
        "data" : data,
        "columns" : [
            { "data" : "adBarcode" },
            { "data" : "adDescription" },
            { "data" : "supplier" },
            { "data" : "adGrouping" },
            { "data" : "adcategory" },
            { "data" : "adcount" },
            {
              targets: 6,
              data: null,
              render: function ( data, type, row ) {
                var retdata;

                if(data.adcount === '0'){
                    retdata = "<button type='button' id = 'btnadd' class='btn btn-info'>Add</button>&nbsp<button type='button' id = 'btnadd' class='btn btn-info'>Cancel</button>";
                }
                else{
                    retdata = "<button type='button' id = 'btnledger' class='btn btn-info'>Transfer</button>&nbsp<button type='button' id = 'btncancel' class='btn btn-info'>Cancel</button>";
                }
                return retdata;
              }
            }
        ],
        "columnDefs": [
        { "width": "170px", "targets": 6 },
          { targets: 5, className: 'dt-body-right' },
        ],
        select:{
            style:'multi',
            selector:'td:first-child'
        },
        order: [[1,'asc']]
        });
             
         //DataTable click event
         $('#tbitem tbody').on( 'click', 'button', function () {
               $('#myModal').modal('show');
        } 
        );
        
        // $('#tbcategory tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
    
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}

function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>Barcode</td>'+
            '<td>Date</td>'+
            '<td>Group</td>'+
            '<td>System on Hand</td>'+
            '<td>Sellout</td>'+
            '<td>Receiving</td>'+
            '<td>Inv Adj</td>'+
            '<td>Selling Area</td>'+
            '<td>Warehouse Area</td>'+
            '<td>Variance</td>'+
        '</tr>'+
        '<tr>'+
            '<td>AKDWOLXO29902</td>'+
            '<td>12/20/2019</td>'+
            '<td>Kitchenware</td>'+
            '<td>20</td>'+
            '<td>6</td>'+
            '<td>10</td>'+
            '<td>20</td>'+
            '<td>10</td>'+
            '<td>13</td>'+
            '<td>2</td>'+
        '</tr>'+
        '<tr>'+
            '<td>ASKEWLSLXLXXLL</td>'+
            '<td>01/03/2020</td>'+
            '<td>Kitchenware</td>'+
            '<td>20</td>'+
            '<td>500</td>'+
            '<td>9</td>'+
            '<td>0</td>'+
            '<td>11</td>'+
            '<td>156</td>'+
            '<td>2</td>'+
        '</tr>'+
    '</table>';
}


function SCHEDULE_LOAD()
{
    $.ajax({
      url:"php/item/SYS_Report_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_CATEGORY = $('#tbschedule').DataTable ({
             "autoWidth": false,
        "data" : data,
        "columns" : [
          {
                'className':      'details-control',
                'orderable':      false,
                'data':           null,
                'defaultContent': ''
            },
            { "data" : "reportname" },
            // { "data" : "datefrom" },
            // { "data" : "dateto" },
        ],

        order: [[1,'asc']]
        });

         $('#tbschedule tbody').on('click', 'td.details-control', function () {
          var tr = $(this).closest('tr');
          var row = DT_CATEGORY.row(tr);

          if (row.child.isShown()) {
              // This row is already open - close it
              row.child.hide();
              tr.removeClass('shown');
          } else {
              // Open this row
              row.child(format({
                  'Key 1' : tr.data('key-1'),
                  'Key 2' :  tr.data('key-2'),
                  'Key 3' :  tr.data('key-3'),
                  'Key 4' :  tr.data('key-4'),
                  'Key 5' :  tr.data('key-5'),
                  'Key 6' :  tr.data('key-6'),
                  'Key 7' :  tr.data('key-7'),
                  'Key 8' :  tr.data('key-8'),
              })).show();
              tr.addClass('shown');
          }
      });

         $('#btn-show-all-children').on('click', function(){
        // Enumerate all rows
        table.rows().every(function(){
            // If row has details collapsed
            if(!this.child.isShown()){
                // Open this row
                this.child(format(this.data())).show();
                $(this.node()).addClass('shown');
            }
        });
    });

    // Handle click on "Collapse All" button
    $('#btn-hide-all-children').on('click', function(){
        // Enumerate all rows
        table.rows().every(function(){
            // If row has details expanded
            if(this.child.isShown()){
                // Collapse row details
                this.child.hide();
                $(this.node()).removeClass('shown');
            }
        });
    });
             

        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}