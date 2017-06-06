#CCCSD
-------

The clinician/consumer custom solution development environment enables people without a technical background to profit from the AsTeRICS framework. It consists of a database of AsTeRICS models and a step-by-step wizard for choosing a suitable model. For more information on AsTeRICS, please refer to [AsTeRICS on Github] (https://github.com/asterics/AsTeRICS) or the AsTeRICS website [http://www.asterics.eu](http://www.asterics.eu).

##Docs

The CCCSD consists of two major parts: a web-hosted model repository (backend) and the actual wizard (frontend).

The CCCSD model repository is a constantly growing database of AsTeRICS models, for which the original developer has defined certain metadata that help identify the usefulness of this specific model for a certain user. These data are:
•	Model name
•	Model description: What does the model do?
•	Device category: What will the end user want to control, e.g. computer, environment, etc.?
•	Body functions: Which function(s) of the users body can or will be used for input, e.g. head, arm, muscle signals, etc.?
•	Technical prerequisites: What hardware is already available or what is the user ready to buy, e.g. webcam, button, etc.?
The repository also has its own user administration. Only registered users can upload models to the database. A user has to give his name and email address to be able to register and can then define a username and a password. Each user has a certain role – default is “normal user”, an admin has the right to assign admin rights to another user. Also the admin is allowed to set the “approved”-flag for models uploaded by a normal user. This means that this model has been checked by an expert and approved as useful.

The step-by-step wizard comprises the following steps:
1.	What do you want to control?
2.	What hardware do you have available? (this step can be skipped, if the user does not care what hardware shall be required for the model)
3.	What body functions can you / do you want to use?
The wizard then gives the user a list of suitable models, the details of which can be viewed by clicking the model name. For each model three options will be provided:
•	Download: downloads the model file in .acs-format to the local hard drive
•	Send to ACS: opens the model in the WebACS (see https://github.com/asterics/WebACS)
•	Send to ARE: directly deploys the model to the ARE, ready to use (for the latest ARE, see https://github.com/asterics/AsTeRICS)
For the communication between the wizard and the ARE the AsTeRICS REST-library is used.

To access the backend, please open ..\Backend\php\Login.php. To access the frontend, please open ..\Frontend\HTML\index.html. 

The CCCSD is currently optimised for Firefox (53.0.3 or higher).

##Licence

Unless mentioned otherwise, the CCCSD is licensed under the Apache License, Version 2.0. You may obtain a copy of the license at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0 "Apache Licence 2.0")
 
This software is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.

##Acknowledgement
This project has received funding from the European Union’s Seventh Framework Programme for research, technological development and demonstration under grant agreement no 610510. Visit [developerspace.gpii.net] (http://developerspace.gpii.net/) to find more useful resources.