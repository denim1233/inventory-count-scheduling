$(document).ready(function(){

    // $("#btnupload").click(function() { UPLOAD_FILE(); });
    $("#btnupload").click(function() { alert('button works'); });

});

function UPLOAD_FILE()
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
