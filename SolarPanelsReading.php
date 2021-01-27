<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="a4-try.css" media="screen, print">
        <title>SolarPanels</title>
    </head>
    <body>
        <div id="header">
			<img id="left" src="LOGO-91X95.png" alt="logo" />
			<h1 id="mid">Cwmaman Town Council<br>Solar Panels<br>Reading</h1>
			<img id="right" src="LOGO-91X95.png" alt="logo" />
		</div>
<?php 
// change this to a location apropriate for your system, the file does not need to exist
// but you must have write access to the directory.
$tmpDir = getenv('/SolarPanels');
if (!$tmpDir)
    $tmpDir = "/SolarPanels";
$dataFile = $tmpDir."/SolarPanels.dat";


class SolarPanels {
    // class variable definitions
    var $gb_dat;
    var $data;
    
    /*
       SolarPanels constructor
       initializes SolarPanels data
     */
    function SolarPanels($dataFile) {
        $this->gb_dat = $dataFile;
        $this->data = "";
        $this->_getData();
        
        // if data was posted to the script, lets add an entry to the SolarPanels
        if ($_SERVER["REQUEST_METHOD"]=="POST") {
            if (!$this->addSolarPanelsEntry()) {
                echo("Error in posting to the SolarPanels, please use <a href=\"".$_SERVER["PHP_SELF"]."\">".$_SERVER["PHP_SELF"]."</a> to post your entry.<br><br><hr>\n");
            }
        }        
        if ($this->data) $this->outputData();
        $this->outputForm();
    }

    /*
        _getData
        reads the data from the SolarPanels data file
     */
    function _getData() {
        $lines = @file($this->gb_dat);
        if ($lines) {
            $this->data = join($lines, "\n");
        }
    }
    
    /*
       outputData
       writes the contents of the SolarPanels data file to stdout
    */
    function outputData() {
        echo $this->data;
    }

        /*
         _createEntryHTML
         use data from the post to create an HTML sniplet
        */
    function _createEntryHTML() {
        /**    
        Get the posted data 
        */
        $name = $_POST["name"];
        $date = $_POST["date"];
        $building = $_POST["building"];
        $reading = $_POST["reading"];
        
        // just a little validation, in the real world, real validation should be done
        if (!$name || !$reading) {
            echo ("You did not enter your name or reading, please resubmit your entry.<br><br><hr>\n");
            return null;
        }
        
        // get the current time
        $today = date("F j, Y, g:i a");
        
        // build the html for the posted entry
        $data = "Posted: <b>$today</b> by <b>$name</b> &lt;$date&gt;<br>".
            "building: $building<br>\n".
            "<p>$reading</p><br><hr>\n";
        
        return $data;
    }
    
    /*
       _writeDataFile
       write the data back to the datafile
     */
    function _writeDataFile() {
        // open and clear the file of it's contents
        $f = @fopen($this->gb_dat, "w");
        if (!$f) {
            echo ("Error opening $this->gb_dat.<br>\n");
            return false;
        }
        // write the new file    
        if (fwrite($f, $this->data) < 0) {
            echo ("Error writing data to $this->gb_dat.<br>\n");
        }
        fclose($f);
        return true;
    }
    
    /*
       addSolarPanelsEntry
       this function formats the post data into html, and adds it
       to the data file
     */
    function addSolarPanelsEntry() {
        $entry = $this->_createEntryHTML();
        if (!$entry) return false;
        $this->data = $entry.$this->data;
        return $this->_writeDataFile();
    }

    function outputForm() {
        // below is our entry form for adding a SolarPanels entry
        // we insert the link to this page so the form will work, no matter
        // where we put it, or what we call it.
        ?>
        <a name="post"><b>Please submit reading</b></a><br> 
        <form action="<?php echo($_SERVER["PHP_SELF"]);?>" method="POST"> 
        Name: <input type="Text" name="name" size="40" maxlength="50"><br> 
        Date:  <input type="date" name="date" size="35" maxlength="40"><br> 
        Building: <input type="Text" name="building" size="35" maxlength="40"><br> 
        Reading: <input type="Text" name="reading" size="35" maxlength="40"><br> 
        <br> 
        <input type="Submit" name="action" value="Submit"> 
        <input type="reset"> 
        </form> 
        <?php
    }
}

// create an instance of the SolarPanels,
// the SolarPanels constructor handles everything else.
$gb = new SolarPanels($dataFile);
?>
<a href="index.html" name="home" id="home">Home</a>
</body>
</html>
