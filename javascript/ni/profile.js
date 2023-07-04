var moviedata;
var jsondata;
var alib = new al();

$(document).ready(function(){
    //console.log(localStorage.getItem('obj'));
    alib.LOAD_NAVBAR('topnav','indexnavbar.html');
    moviedata = localStorage.getItem('obj');
    jsondata = JSON.parse(moviedata);
    SET_DATA();
    COUNT_VIEW();
    
     $('#topnav').on('click', 'a', function(){
         if(this.id == 'alogin')
         {
             $('#myModal').modal('show'); 
         }
    });
    
    $('#btnenter').click(function()
    { 
       LOGIN();
    });
    
        $('#txtuserpassword').on("keypress", function(e) {
        if (e.keyCode == 13) {
           LOGIN();
        }
    });
    
    alib.LOAD_NAVBAR('footer','footer.html');
    
});

function LOGIN()
{
    var p1 = document.getElementById('txtusername').value;
    var p2 = document.getElementById('txtuserpassword').value;
    alib.USER_LOGIN("php/user/SYS_User_Login.php",{vusername:p1,vuserpassword:p2},alib.START_SESSION);
}

function SET_DATA()
{
    document.getElementById('pbprofile').src = jsondata[0].moviepicture;
    document.getElementById('lbdescription').innerHTML = jsondata[0].moviedescription;
    document.getElementById('lbmoviename').innerHTML = jsondata[0].moviename;
    document.getElementById('lbcategory').innerHTML = jsondata[0].moviecategory;
}

function COUNT_VIEW()
{
    $.ajax({
            url:"php/movies/PM_Movie_Count.php",
            method:"POST",
            data :{varmovieid:jsondata[0].movieid},
            dataType: "JSON",
            success:function(data)
            {
                //console.log(data);
            },
            error: function(data) 
            {
              alert('request error!');
            },
    });
}
