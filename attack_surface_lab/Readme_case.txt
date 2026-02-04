Case Study: “The Open Student Records API”
Scenario (story) 1: 

A university web application has a login page for students.
However, the developer assumes that users will always come through the login page and does not enforce authentication on backend APIs.
An attacker directly accesses the API URL using a browser or curl and retrieves student details without logging in.  

In real world: 
Missing / Weak Authentication — Named Real Incidents
🔴 Facebook Access Token Exposure (2018)

Company: Facebook
Issue type: Missing / improper authentication checks
What happened:

Certain backend endpoints trusted access tokens without sufficient validation.

Attackers accessed user data without proper authorization.

Impact:

~50 million accounts affected

Why this fits your scenario:

Backend trusted requests without enforcing strong authentication consistently.

Scenario (story) 2: 

A shopping website creates a session for every visitor, even before login.
When the user logs in, the application continues using the same session ID instead of generating a new one.
If an attacker already knows that session ID, they can reuse it after the victim logs in.

ING Direct Banking Session Fixation (2009)

Company: ING Direct
Issue type: Session fixation
What happened:

Login process did not regenerate session ID

Attackers could reuse known session IDs

Impact:

Risk of unauthorized account access

Why it matters:

Banking systems were affected — shows severity

Story 3:
A user logs into a web application from a shared computer.
After finishing work, the user clicks Logout and leaves.
The next person presses the browser’s Back button and can still access the dashboard because the session was never destroyed.

University ERP / LMS Logout Vulnerabilities (Multiple reported cases)

Systems: Moodle, custom ERPs
Issue type: Missing logout protection
What happened:

Logout redirected user

Session cookie not destroyed

Impact:

Student record exposure

Extremely relatable to academic environments