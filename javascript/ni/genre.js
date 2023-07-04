var GENREID;
var ROW_INDEX;
var COL_INDEX;
var DT_GENRE;
var ROW_DATA;
var alib;
    
$(document).ready(function(){
    alib = new al();
    alib.CHECK_SESSION();
    GENRE_LOAD();
    alib.LOAD_NAVBAR('topnav','adminnavbar.html'); 
    $("#btnadd").click(function() { CLEAR_DATA(); });
    $("#btnsave").click(function() { GENRE_MANAGERECORD();});
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
    GENREID = 0;
    document.getElementById("txtgenre").value = "";
    document.getElementById("txtdescription").value = "";
    $('#myModal').modal('show');
}

function GENRE_MANAGERECORD()
{
    var p1 = document.getElementById("txtgenre").value;
    var p2 = document.getElementById("txtdescription").value;
    $.ajax({
            url:"php/genre/SYS_Genre_ManageRecord.php",
            method:"POST",
            data :{vgenreid:GENREID, vgenrename:p1,vgenredescription:p2},
            dataType: "JSON",
            success:function(data)
            {
                console.log(data);
              if(data[0]['statusid'] == '1')
                {
                    if(GENREID === 0)
                    {
                         var INSERT = 
                        {
                        "GenreId" : data[0]['pgenreid'],
                        "GenreName" : p1,
                        "GenreDescription" : p2,
                        };
                        DT_GENRE.row.add(INSERT).draw();
     
                       alert('data saved!');
                    }
                    else
                    {
                        var UPDATE = 
                        {
                        "GenreId" : GENREID,
                        "GenreName" : p1,
                        "GenreDescription" : p2,
                        };
                        
                        DT_GENRE.row(ROW_DATA).data(UPDATE).draw(); 
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
              alert('saving error!');
            },
    });
}


function GENRE_LOAD()
{
    $('#b1').remove();
    $.ajax({
      url:"php/genre/SYS_Genre_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_GENRE = $('#tbgenre').DataTable ({
        "scrollY":        "600px",
        "scrollCollapse": true,
        "paging":         false,
        "data" : data,
        "columns" : [
            { "data" : "GenreId" },
            { "data" : "GenreName" },
            { "data" : "GenreDescription" },
            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-info'>Edit</button> &nbsp&nbsp<button type='button' class='btn btn-danger'>Delete</button>"    
            },
            ],
        "columnDefs": [
            { "width": "13%", "targets": 3 },
            { "width": "20%", "targets": 1 },
            { "visible": false, "targets": 0 }
            ]
             });
             
         //DataTable click event
         $('#tbgenre tbody').on( 'click', 'button', function () {
        var data = DT_GENRE.row( $(this).parents('tr') ).data();
        
        if(this.className == 'btn btn-info')
             {
                //load data into modal
                GENREID = data.GenreId;
                document.getElementById("txtgenre").value  = data.GenreName;
                document.getElementById("txtdescription").value  = data.GenreDescription;
                
                //show modal
                $('#myModal').modal('show');
             }
        } 
        );
        
        $('#tbgenre tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
    
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}
