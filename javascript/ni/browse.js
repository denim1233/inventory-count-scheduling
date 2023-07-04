var alib = new al();

$(document).ready(function(){
    
    alib.LOAD_NAVBAR('topnav','indexnavbar.html'); 
    MOVIE_LOAD();
    
    $('#btnenter').click(function() { LOGIN(); });
    alib.LOAD_NAVBAR('footer','footer.html');
    
    $('#topnav').on('click', 'a', function(){
        if(this.id == 'alogin') { $('#myModal').modal('show'); }
    });
    
      $('#txtuserpassword').on("keypress", function(e) {
        if (e.keyCode == 13) {
           LOGIN();
        }
    });

});

function LOGIN()
{
    var p1 = document.getElementById('txtusername').value;
    var p2 = document.getElementById('txtuserpassword').value;
    alib.USER_LOGIN("php/user/SYS_User_Login.php",{vusername:p1,vuserpassword:p2},alib.START_SESSION);
}

function MOVIE_LOAD()
{
    $('#b1').remove();
    $.ajax({
      url:"php/movies/PM_Movies_Get.php",
      method:"POST",
    //   data :{varempid:empid},
      dataType: "JSON",
      
      success:function(data)
      {
        console.log(data);
        DT_MOVIE = $('#tbbrowse').DataTable ({
        "pageResize": true,
        // "scrollY":        "600px",
        // "scrollCollapse": true,
        "paging":         true,
        "data" : data,
        "columns" : [
            { "data" : "MovieName" },
            { "data" : "MovieDescription" },
            { "data" : "CategoryName" },
            ],
           
          "columnDefs": [
                { "width": "25%", "targets": 0 },
                { "width": "10%", "targets": 2 }
                //{ "width": "10%", "targets": 3 }
                ]
             });
        },
        error: function(data) 
         {
            alert('error!');
         }
    });
}
