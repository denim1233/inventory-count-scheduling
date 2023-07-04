var CATEGORYID;
var ROW_DATA;
var alib;

$(document).ready(function(){
    alib = new al();
    alib.CHECK_SESSION();
    CATEGORY_LOAD();
    alib.LOAD_NAVBAR('topnav','adminnavbar.html'); 
    $("#btnadd").click(function() { CLEAR_DATA(); });
    $("#btnsave").click(function() { CATEGORY_MANAGERECORD(); });
    alib.LOAD_NAVBAR('footer','footer.html');
    
    $('#topnav').on('click', 'a', function()
    {
        if(this.id == 'alogout')
        {
             alib.DESTROY_SESSION('php/settings/sessiondestroy.php','http://apitinfo.ml/index');
        }
    });
});

function CLEAR_DATA()
{
    CATEGORYID = 0;
    document.getElementById("txtcategory").value = "";
    $('#myModal').modal('show');
}

function CATEGORY_MANAGERECORD()
{
    var p1 = document.getElementById("txtcategory").value;
    $.ajax({
            url:"php/category/SYS_Category_ManageRecord.php",
            method:"POST",
            data :{vcategoryid:CATEGORYID, vcategoryname:p1},
            dataType: "JSON",
            success:function(data)
            {
                console.log(data);
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
        "columns" : [
            { "data" : "CategoryId" },
            { "data" : "CategoryName" },
            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-info'>Edit</button> &nbsp&nbsp<button type='button' class='btn btn-danger'>Delete</button>"    
            },
            ],
        "columnDefs": [
            { "width": "13%", "targets": 2 },
            { "width": "80%", "targets": 1 },
            { "visible": false, "targets": 0 }
            ]
             });
             
         //DataTable click event
         $('#tbcategory tbody').on( 'click', 'button', function () {
             
             var data = DT_CATEGORY.row( $(this).parents('tr') ).data();
             
             if(this.className == 'btn btn-info')
             {
                //load data into modal
                CATEGORYID = data.CategoryId;
                document.getElementById("txtcategory").value  = data.CategoryName;
                
                //show modal
                $('#myModal').modal('show');
             }
        } 
        );
        
        $('#tbcategory tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
    
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}