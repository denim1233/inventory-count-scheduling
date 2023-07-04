var MOVIE_ID;
var UPLOAD_PICTUREPATH;
var DT_MOVIE;
var ROW_DATA;
var ROW_ITEMS;
var DT_MOVIE_GENRE;
var MOVIE_GENRE_ID;
var alib;

$(document).ready(function(){
    alib = new al();
    alib.CHECK_SESSION();
    alib.LOAD_NAVBAR('topnav','adminnavbar.html'); 
    alib.HIDE_ELEMENT('txtpicture');
    
    MOVIE_LOAD();
    
    alib.LOAD_DATA_IN_COMBOBOX('cmbcategory','p','php/category/SYS_Category_Get','CategoryId','CategoryName');
    alib.LOAD_DATA_IN_COMBOBOX('cmbmoviegenre','p','php/genre/SYS_Genre_Get','GenreId','GenreName');
    
    $("#btnadd").click(function() { CLEAR_DATA(); });
    $("input").change(function(){ alib.LOAD_PICTURE_FROM_INPUT(this,'pbmovie'); });
    $("#btnsave").click(function() { MOVIE_MANAGERECORD();});
    $("#btnsavegenre").click(function() { MOVIE_GENRE_INSERT();});
    
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
    MOVIE_ID = 0;
    document.getElementById("btnbrowse").value = "";
    document.getElementById("txtpicture").value = "";
    document.getElementById("txtmovie").value = "";
    document.getElementById("txtdescription").value = "";
    document.getElementById("cbfeature").checked = false;
    alib.LOAD_PICTURE_FROM_PATH('pbmovie','');
    $('#myModal').modal('show');
}

function MOVIE_MANAGERECORD()
{
    //clear data when opening a record and adding
    UPLOAD_PICTUREPATH = '';
    var cmb = document.getElementById("cmbcategory");
    var filename = alib.GET_FILENAME('btnbrowse'); 
    var p1 = document.getElementById("txtmovie").value;
    var p2 = filename;
    var p3 = document.getElementById("txtdescription").value;
    var p4 = document.getElementById("cmbcategory").value;
    var p5;
    
    if(document.getElementById("cbfeature").checked ===  true)
    {
        p5 = '1';
    }
    else
    {
        p5 = '0';
    }
    
    UPLOAD_PICTUREPATH = 'images/'+p2;
    var f;
    var fvar = new FormData(); 
    var cmbtext = cmb.options[cmb.selectedIndex].text;
    //  alert(cmbtext);
     
    if( document.getElementById("btnbrowse").files.length === 0 )
    {
     UPLOAD_PICTUREPATH = document.getElementById("txtpicture").value;
    }
    else
    {
      f = document.getElementById('btnbrowse').files[0];
    }

    fvar.append('vmovieid',MOVIE_ID);
    fvar.append('vmoviename',p1);
    fvar.append('vmoviepicture',UPLOAD_PICTUREPATH);
    fvar.append('vmoviedescription',p3);
    fvar.append('vmoviecategoryid',p4);
    fvar.append('vmoviefeature',p5);
    fvar.append('file',f );
       
  $.ajax({
  
        method:"POST",
        data :fvar,
        dataType: "JSON",
        processData: false,
        cache: false,
        contentType: false,
        url:"php/movies/PM_Movies_ManageRecord.php",
        success:function(data)
        {
            if(data[0]['statusid'] == '1')
            {
                //stop when there is duplicate record
                 if(MOVIE_ID === 0)
                {
                    var INSERT = 
                    {
                    "MovieId" : data[0]['pmovieid'],
                    "MovieName" : p1,
                    "MovieDescription" : p3,
                    "MoviePicture" : UPLOAD_PICTUREPATH,
                    "CategoryName" : cmbtext,
                    "CategoryId" : p4,
                    "MovieFeature" : '1'
                    };
                    
                    DT_MOVIE.row.add(INSERT).draw();
                    alert('data succesfully added!');
                }
                else
                {
                    var UPDATE = 
                    {
                    "MovieId" : MOVIE_ID,
                    "MovieName" : p1,
                    "MovieDescription" : p3,
                    "MoviePicture" : UPLOAD_PICTUREPATH,
                    "CategoryName" : cmbtext,
                    "CategoryId" : p4,
                    "MovieFeature" : p5
                    };
        
                    DT_MOVIE.row(ROW_DATA).data(UPDATE).draw();  
                    alert('data successfully updated!');
                }
            }
            else
            {
                alert(data[0]['querystatus']);
            }
        },
        error: function(data) 
        {
            console.log(data);
        },
    });
}

function MOVIE_LOAD()
{
    $.ajax({
      url:"php/movies/PM_Movies_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_MOVIE = $('#tbmovie').DataTable ({
        "scrollY":        "600px",
        "scrollCollapse": true,
        "paging":         false,
        "data" : data,
        "columns" : [
            { "data" : "MovieId" },
            { "data" : "MovieName" },
            { "data" : "MovieDescription" },
            { "data" : "MoviePicture" },
            { "data" : "CategoryName" },
            { "data" : "CategoryId" },
            { "data" : "MovieFeature" },
            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-info'>Edit</button> &nbsp&nbsp<button type='button' class='btn btn-danger'>Delete</button>&nbsp&nbsp<button type='button' class='btn btn-success'>Genre</button>"    
            },
            ],
        "columnDefs": [
            { "width": "17%", "targets": 7 },
            { "width": "20%", "targets": 1 },
            { "visible": false, "targets": 0 },
            { "visible": false, "targets": 3 },
            { "visible": false, "targets": 5 },
            { "visible": false, "targets": 6 },
            ],
             "initComplete": function(settings, json) {
            
            }
             });
             
         //DataTable click event
         $('#tbmovie tbody').on( 'click', 'button', function () {
        ROW_ITEMS = DT_MOVIE.row( $(this).parents('tr') ).data();
        MOVIE_ID = ROW_ITEMS.MovieId;
        if(this.className == 'btn btn-info')
        {
            document.getElementById("cbfeature").disabled = false;
            //load data into modal
            document.getElementById("txtmovie").value  = ROW_ITEMS.MovieName;
            document.getElementById("txtdescription").value  = ROW_ITEMS.MovieDescription;
            document.getElementById("cmbcategory").value = ROW_ITEMS.CategoryId;
            alib.LOAD_PICTURE_FROM_PATH('pbmovie',ROW_ITEMS.MoviePicture);
            document.getElementById("txtpicture").value =  ROW_ITEMS.MoviePicture;
            
            if(ROW_ITEMS.MovieFeature == '1')
            {
                document.getElementById("cbfeature").checked =  true;
            }
            else
            {
                document.getElementById("cbfeature").checked =  false;
            }
     
            //show modal
            $('#myModal').modal('show');
        
        }
        else if(this.className == 'btn btn-success')
        {
            MOVIE_GENRE_LOAD();
            document.getElementById("lbmovie").innerHTML = ROW_ITEMS.MovieName;
            $('#genremodal').modal('show');
        }

        } 
        );
        
        $('#tbmovie tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}

function MOVIE_GENRE_LOAD()
{
    $('#tb_moviegenre_body').remove();
    $.ajax({
    url:"php/moviegenre/PM_MovieGenre_Get.php",
    method:"POST",
    data :{vmovieid:MOVIE_ID},
    dataType: "JSON",
      
    success:function(data)
    {
        if(data.status[0].statusid == 1)
        {
            DT_MOVIE_GENRE = $('#tbmoviegenre').DataTable ({
            "language": { search: '', searchPlaceholder: "Search..." },
            "searching": false,
            "bDestroy": true,
            "scrollY":        "600px",
            "scrollCollapse": true,
            "paging":         false,
            "data" : data.dbdata,
            "columns" : [
            { "data" : "MovieGenreId" },
            { "data" : "GenreName" },
            {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-danger'>Delete</button>"
            },
            ],
            "columnDefs": [
            { "width": "80%", "targets": 1 },
            { "width": "20%", "targets": 2 },
            { "visible": false, "targets": 0 },
            ],
            "initComplete": function(settings, json) {
            
            }
            });
            
            //DataTable click event
            $('#tbmoviegenre tbody').on( 'click', 'button', function () {
            ROW_ITEMS = DT_MOVIE_GENRE.row( $(this).parents('tr') ).data();
            MOVIE_GENRE_ID = ROW_ITEMS.MovieGenreId;
            
            if(this.className == 'btn btn-danger')
            {
                if (confirm('Are you sure you want delete this?')) 
                {
                    MOVIE_GENRE_DELETE(DELETE_ROW);
                } 
            }
        } 
        );
        
        $('#tbmoviegenre tbody').on( 'click', 'tr', function () { ROW_DATA = this;} );
        
    }
    },
    error: function(data) 
    {
        console.log(data.status[0].statusname);
    }
    });
}

function MOVIE_GENRE_INSERT()
{
    var p1 = document.getElementById("cmbmoviegenre").value;
    var p2 = MOVIE_ID;
    
  $.ajax({
    method:"POST",
    data :{vmoviegenreid:p1,vmovieid:p2},
    dataType: "JSON",
    url:"php/moviegenre/PM_MovieGenre_Insert.php",
    success:function(data)
    {
        console.log(data);
        if(data.status[0].statusid == '1')
        {
            if(data.dbdata[0].statusid == '1')
            {
             var INSERT = 
                {
                "MovieGenreId" : data.dbdata[0]['pmoviegenreid'],
                "GenreName" : alib.CMB_TEXT_GET('cmbmoviegenre'),
                };
                
                DT_MOVIE_GENRE.row.add(INSERT).draw();
                alert('data succesfully added!');
            }
            else
            {
                alert(data.dbdata[0].querystatus);
            }
        }
        else
        {
            alert(data.status[0].statusname);
        }
    },
    error: function(data) 
    {
        console.log(data);
    },
        });
}

function DELETE_ROW()
{
    DT_MOVIE_GENRE.row(ROW_DATA).remove().draw(true);
}

function MOVIE_GENRE_DELETE(callback)
{
  $.ajax({
    method:"POST",
    data :{vmoviegenreid:MOVIE_GENRE_ID},
    dataType: "JSON",
    url:"php/moviegenre/PM_MovieGenre_Delete.php",
    success:function(data)
    {
        console.log(data);
        if(data.status[0].statusid == '1')
        {
            callback();
        }
        else
        {
            console.log(data);
        }
    },
    error: function(data) 
    {
        console.log(data);
    },
        });
}