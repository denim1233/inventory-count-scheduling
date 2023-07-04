class al
{
    pagename()
    {
        var path = window.location.pathname;
        var page = path.split("/").pop();
        return page;
    }
    
    addclass(elementid,classname)
    {
        $("#topnav").load("adminnavbar.html", function(){
        elementid = '#'+elementid;
        $(elementid).addClass(classname);
        });
    }
    
    
    LOAD_NAVBAR(divcontainer,filename)
    {
        try
        {
            divcontainer = '#'+divcontainer;
            var elementid = this.pagename();
            elementid = '#'+elementid;
            
            $(divcontainer).load(filename, function(){
                        $(elementid).addClass("active");
            });
            }
        catch(err)
        {
            
        }
    }
    
    //Load into picture from input button
    LOAD_PICTURE_FROM_INPUT(input,id_element) 
    {
        if (input.files && input.files[0]) 
        {
            var reader = new FileReader();
            id_element = '#'+ id_element;
            reader.onload = function (e) {
                $(id_element)
                    .attr('src', e.target.result)
                    .width(200)
                    .height(250);
            };
        
            reader.readAsDataURL(input.files[0]);
        }
    }
        
    //Load path string
    LOAD_PICTURE_FROM_PATH(elementid,path)
    {
        //alert(path);
        document.getElementById(elementid).src= path;
    }
    
    //Get the file name from input element "FILE NAME ONLY!"
    GET_FILENAME(element)
    {
        var fullPath = document.getElementById(element).value;
        if (fullPath) 
        {
            var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) 
            {
                filename = filename.substring(1);
            }
            //alert(filename);
            return filename;
        }
    }
    
    LOAD_DATA_IN_COMBOBOX(element,parameter,phpfile,col1,col2)
    {
      $.ajax({
                  url:phpfile,
                  method:"POST",
                //   data :parameter,
                  dataType: "JSON",
                  success:function(data)
                  {
                      console.log(data);
                    $.ctr = 0;
                    var select = document.getElementById(element);
                    
                    select.innerHTML = "";
                    while($.ctr < data.length)
                    {
                        var option = document.createElement('option');
                        option.value = data[$.ctr][col1];
                        option.text = data[$.ctr][col2];
                        select.add(option, $.ctr);
                        $.ctr = $.ctr + 1;
                    }
                  },
                     error: function(data) 
                     {
                        console.log('LOAD_DATA_IN_COMBOBOX -- error');
                     },
      });
      
    }
    
    //CONVERT DATE TO SHORT DATE FORMAT
    CONVERT_TO_DATE(value,format)
    {
        value = new Date();
        var dd = value.getDate();
        var mm = value.getMonth() + 1; //January is 0!
        var yyyy = value.getFullYear();
        
        if (dd < 10) 
        {
            dd = '0' + dd;
        } 
        if (mm < 10) 
        {
            mm = '0' + mm;
        } 
        
        if(format === 'mm/dd/yyyy')
        {
            value = mm + '/' + dd + '/' + yyyy;
        }
        else if(format === 'dd/mm/yyyy')
        {
            value = dd + '/' + mm + '/' + yyyy;
        }
        else if(format === 'mm-dd-yyyy')
        {
            value = mm + '-' + dd + '-' + yyyy;
        }
           else if(format === 'dd-mm-yyyy')
        {
            value = dd + '-' + mm + '-' + yyyy;
        }
        
        //alert(value);
        return value;
    }
    
    HIDE_ELEMENT(element)
    {
        if(element !== '')
        {
            var x = document.getElementById(element);
            if (window.getComputedStyle(x).visibility === "visible") 
            {
                x.style.visibility = "hidden";
            }
            else
            {
                x.style.visibility = "block";
            }
        }
        else
        {
            alert('element id is missing');
        }
    }
      hidecolumn(classname)
    {
        var ids = document.getElementsByClassName(classname);
        for (var i = 0; i < ids.length; i++) 
        {
            ids[i].style.display = "none";
        }
    }
    
    CHECK_SESSION()
    {
        $.ajax({
            url:"php/settings/checksession.php",
            method:"POST",
            //data :{varmovieid:jsondata[0].movieid},
            dataType: "JSON",
            success:function(data)
            {
                //console.log(data);
                if(data.sessionexists === 'false')
                {
                    window.location.href="http://apitinfo.ml/";
                }
            },
            error: function(data) 
            {
              console.log('CHECK_SESSION -- error');
            },
        });
    }
    
    DESTROY_SESSION(phppath,urlto)
    {
        $.ajax({
        url:phppath,
        method:"POST",
        dataType: "JSON",
        success:function(data)
        {
          window.location.href= urlto;
        },
        error: function(data) 
        {
            console.log('DESTROY_SESSION -- error');
        },
        });
    }
    
    USER_LOGIN(phppath,parameter,callback)
    {
         var validate ;
        $.ajax({
            url:phppath,
            data :parameter,
            method:"POST",
            dataType: "JSON",
          
            success:function(data)
            {
                 callback(data);
            },
            error: function(data) 
            {
                alert('connection error!');
                console.log('USER_LOGIN -- error')
            }
        
        });
    }
    
        
     START_SESSION(receiveddata)
    {
        // var jsondata = JSON.parse(data);
        // console.log(data);
        $.ajax({
        url:"php/settings/session.php",
        method:"POST",
        dataType: "JSON",
        success:function(data)
        {
           if(receiveddata[0].StatusId == 1)
            {
                window.location.href="http://apitinfo.ml/admin";
            }
            else
            {
                alert('invalid login details!');
            }
        },
        error: function(data) 
        {
            console.log('START_SESSION -- error');
        },
        });
    }
    
    CMB_TEXT_GET(element)
    {
        if(element !== '')
        {
            var t = document.getElementById(element);
            var textvalue = t.options[t.selectedIndex].text;
            return textvalue;
        }
        else
        {
            console.log('CMB_GET_TEXT -- Element not found');
        }
       
    }
    
}