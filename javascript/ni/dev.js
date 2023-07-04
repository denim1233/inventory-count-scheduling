var DT_FILES;
var ROW_DATA;
var alib;

$(document).ready(function(){
    
    alib = new al();
    alib.LOAD_NAVBAR('topnav','adminnavbar.html'); 
    FILE_LOAD();
    alib.LOAD_NAVBAR('footer','footer.html');
    $("#btnadd").click(function() { CLEAR_DATA(); });
    $("#btnsave").click(function() { FILE_UPLOAD(); });
    
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
    document.getElementById("btnbrowse").value = "";
    $('#myModal').modal('show');
}

function FILE_LOAD()
{
    $.ajax({
      url:"php/settings/file_list.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        // console.log(data);
        DT_FILES = $('#tbfiles').DataTable ({
        "autoWidth": false,
        "data" : data,
        "columns" : [
            { "data" : "filename" },
            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-danger'>Delete</button>&nbsp&nbsp<button type='button' class='btn btn-info'>Download</button>"    
            },
            ],
        "columnDefs": [
            { "width": "80%", "targets": 0 },
            { "width": "15%", "targets": 1 }
            ]
             });
             
         //DataTable click event
         $('#tbfiles tbody').on( 'click', 'button', function () {
             var data = DT_FILES.row( $(this).parents('tr') ).data();
             
             if(this.className == 'btn btn-danger')
             {
                if (confirm('Are you sure you want delete this?')) 
                {
                    FILE_DELETE(data.filename,ROW_DELETE);
                } 
             }
             else if(this.className == 'btn btn-info')
             {
                //  FILE_DOWNLOAD(data.filename);
                 window.location.href="/php/settings/file_download.php?filename=" + data.filename;
             }
        } 
        );
        
        $('#tbfiles tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
    
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}

function ROW_DELETE()
{
    DT_FILES.row(ROW_DATA).remove().draw(true);
}

function FILE_DELETE(varfilename,callback)
{
    $.ajax({
        url:"php/settings/file_delete.php",
        method:"POST",
        data :{vfilepath:varfilename},
        dataType: "JSON",
        success:function(data)
        {
          console.log(data);
          if(data[0].statusid == 1)
          {
              callback();
          }
          
        }
    });
}

function FILE_UPLOAD()
{
    var file;
    var fvar = new FormData(); 

    if( document.getElementById("btnbrowse").files.length === 0 )
    {
     alert('there is no file selected');
     return;
    }
    else
    {
      file = document.getElementById('btnbrowse').files[0];
    }

    fvar.append('file',file);
       
  $.ajax({
  
        method:"POST",
        data :fvar,
        dataType: "JSON",
        processData: false,
        cache: false,
        contentType: false,
        url:"php/settings/file_upload.php",
        success:function(data)
        {
            if(data[0].statusid == 1)
            {
                var INSERT = 
                {
                "filename" : alib.GET_FILENAME('btnbrowse'),
                };
                
                DT_FILES.row.add(INSERT).draw();
                alert(data[0].status);
            }
            else
            {
                alert(data[0].status);
            }
        },
        error: function(data) 
        {
            console.log(data);
        },
    });
}