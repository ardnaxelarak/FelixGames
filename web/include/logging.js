function createXHR()
{
	var request = false;
	try
	{
		request = new ActiveXObject('Msxml2.XMLHTTP');
	}
	catch (err2)
	{
		try
		{
			request = new ActiveXObject('Microsoft.XMLHTTP');
		}
		catch (err3)
		{
			try
			{
				request = new XMLHttpRequest();
			}
			catch (err1)
			{
				request = false;
			}
		}
	}
	return request;
}

function log_score(scorelist, user)
{
	var args = Array.prototype.slice.call(arguments, 2);
	var xhr=createXHR();
	var parameters="scorelist=" + scorelist + "&name=" + user.trim()
	for (var i = 0; i < args.length; i++)
		parameters += "&col" + (i + 1) + "=" + args[i];
	xhr.open("POST", "include/scorelogger.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	xhr.send(parameters);
}

function writecomment(filename, user, comment)
{
	var xhr = createXHR();
	var parameters = "file=" + filename + "&name=" + user + "&comment=" + comment;
	xhr.open("POST", "comment.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
	xhr.send(parameters);
}
