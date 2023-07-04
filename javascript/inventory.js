var CATEGORYID;
var ROW_DATA;
var alib;
var DT_CATEGORY;

$(document).ready(function(){

    $("#btnadd").click(function() { CLEAR_DATA(); });
    // CATEGORY_LOAD();
   
    $("#btnpost").click(function() { 
          Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Product/s has been posted',
              showConfirmButton: false,
              timer: 1500
            })

          getSelected();

     });


});

function getSelected() {

       // var rows_selected = DT_CATEGORY.column(0).checkboxes.selected();
       // console.log(rows_selected);

   var selectedIds = DT_CATEGORY.columns().checkboxes.selected()[0];
   console.log(selectedIds)

   // selectedIds.forEach(function(selectedId) {
   //     console.log(selectedId);
   // });

}

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
            data :{action:'inventoryonhand'},
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
        DT_CATEGORY = $('#tbcategory').DataTable ({
             "autoWidth": false,
        "data" : data,
        "columns" : 
        [
            { "data" : "scheduleid"},
            { "data" : "barcode" },
            { "data" : "description" },
            { "data" : "scheduledate" },
            { "data" : "groupname" },
            { "data" : null },
            { "data" : null },
            { "data" : null },
            { "data" : null },
            { "data" : "sellingarea" },
            { "data" : "warehousearea" },
            { "data" : null },
            { "data" : "statusid" },
            { "data" : "remarks" },
        ],
            "columnDefs": [
            { "width": "80px", "targets": 2 },
            { "width": "80px", "targets": 3 },
            { "width": "60px", "targets": 4 },
            { "width": "60px", "targets": 7 },

            { targets: 4, className: 'dt-body-right' },
            { targets: 5, className: 'dt-body-right' },
            { targets: 6, className: 'dt-body-right' },
            { targets: 7, className: 'dt-body-right' },
            { targets: 8, className: 'dt-body-right' },
            { targets: 9, className: 'dt-body-right' },
            { targets: 10, className: 'dt-body-right' },
            { targets: 11, className: 'dt-body-right' },

        ],
        // select:{
        //     style:'multi',
        //     selector:'td:first-child'
        // },
        order: [[1,'asc']]
        });
             
         //DataTable click event
        //  $('#tbcategory tbody').on( 'click', 'button', function () {
             
        //      var data = DT_CATEGORY.row( $(this).parents('tr') ).data();
             
        //      if(this.className == 'btn btn-info')
        //      {
        //         //load data into modal
        //         CATEGORYID = data.CategoryId;
        //         document.getElementById("txtcategory").value  = data.CategoryName;
                
        //         //show modal
        //        
        //      }
        // } 
        // );
        
        },
        error: function(data) 
         {
            alert('error!');
         }
    });

 

}