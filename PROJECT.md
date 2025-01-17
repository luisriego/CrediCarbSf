@baseUrl = {{$dotenv BASE_URL}}
@token = {{$dotenv TOKEN}}

### Get All Projects
GET {{baseUrl}}/project
Authorization: Bearer {{token}}

### Get Project by ID
GET {{baseUrl}}/project/{{projectId}}
Authorization: Bearer {{token}}

### Get Projects by Status
GET {{baseUrl}}/project/status/{{status}}
Authorization: Bearer {{token}}

### Get Projects by Type
GET {{baseUrl}}/project/type/{{projectType}}
Authorization: Bearer {{token}}

### Get Project Progress
GET {{baseUrl}}/project/{{projectId}}/progress
Authorization: Bearer {{token}}

### Get Project Milestones
GET {{baseUrl}}/project/{{projectId}}/milestones
Authorization: Bearer {{token}}

### Get Project Statistics
GET {{baseUrl}}/project/statistics
Authorization: Bearer {{token}}

### Get Projects by Date Range
GET {{baseUrl}}/project/range?startDate={{startDate}}&endDate={{endDate}}
Authorization: Bearer {{token}}

### Get Projects by Company
GET {{baseUrl}}/project/company/{{companyId}}
Authorization: Bearer {{token}}

### Get Project Cost Analysis
GET {{baseUrl}}/project/{{projectId}}/cost-analysis
Authorization: Bearer {{token}}