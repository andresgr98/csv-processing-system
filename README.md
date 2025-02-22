## CSV Processing System by Andrés Gómez

This project is a CSV Processing System. It takes a CSV file, no matter the size, and processes it, persisting every row in a MySQL database.

## Architecture and Load Management

The system follows a **decoupled architecture** using **Symfony Messenger** with Redis as a message queue to handle large-scale CSV processing efficiently. The key technologies used are:

 - Symfony Messenger: Message Queue system that manages the processing of the CSV rows.
 - Redis: acts as a Message Broker, allowing multiple workers to process rows in a distributed manner without collisions.
 - MySQL and Doctrine: store the processed data.
 - Docker: allows execution in containers, making the system easily scalable.

With these being the core components making everything work:

- **CSV Processor Service**: Reads the CSV file in batches and dispatches messages.
- **Message Queue (Redis)**: Stores the batch processing jobs.
- **Message Consumer**: Processes batches asynchronously, persisting data in bulk in MySQL.
- **MySQL Database**: Stores the processed CSV data.

This approach:
 1. Ensures scalability, allowing multiple consumers to process the CSV concurrently.
 2. Avoids duplicates, as Redis assigns each message to a single worker. This means workers in different servers do not process the same messages.
 3. Redis + Symfony Messenger ensure an efficient load management, allowing for parallel treatment of data.

## Flow of processing

 1. CSV loading: A Symfony service reads the file. It then splits it in chunks or **batches** to optimize performance. Batch size can be configured by passing it to the script.
 2. Each batch of the CSV file is then converted into a Symfony Messenger queue **message**.
 3. Distributed processing: Each instance of the service can run one or more workers. The workers consume the messages in the Redis queue, processing one message (batch) at a time without repetition.
 4. Thanks to Redis, each worker gets only **pending** tasks and does not process the messages already done by other workers.
 5. Database persistance: The batch is then persisted in the database table. Bulk inserts are used to improve performance.

Multiple instances of the service can be run in different servers, and, granted all of them are accessing the same Redis queue*, they will manage the load automatically.

\* This is not possible with a dockerized system, but using a properly deployed Redis and server instances, all the consumers can access the same Redis queue and process messages in a distributed system.

---

## Installation and Execution

### **Requisites**

- **Docker** and **Docker Compose** installed on your system.

### **Install & Run**

1. **Clone the repository**:
   ```bash
   git clone git@github.com:andresgr98/csv-processing-system.git
   ```
2. **Navigate to the project folder**:
   ```bash
   cd csv-processing-system
   ```
3. **Run the project with Docker**:
   ```bash
   docker-compose up -d --build
   ```

This will start the necessary services: **PHP (Symfony app), MySQL, Nginx, and Redis**.

---

## **Usage**

### **Importing a CSV File**

To process a CSV file, use the following Symfony console command:

> **These commands must be run INSIDE the php-fpm docker container.**

```bash
php bin/console app:import-csv <file_path> <batch_size>
```

- `<file_path>`: Path to the CSV file.
- `<batch_size>`: Number of rows per batch (default: 100).

Example:
```bash
php bin/console app:import-csv ./data.csv 100
```

This command will enqueue the CSV rows into Redis for asynchronous processing.

### **Processing Messages in Queue**

Start the message consumer to process the queued CSV batches:
```bash
php bin/console messenger:consume async -vv
```

You can run multiple consumers in parallel to speed up processing.

### **Checking Message Queue Stats**

To see how many messages are in the queue:
```bash
php bin/console messenger:stats
```

To clear the queue:
```bash
docker exec -it redis-server redis-cli FLUSHALL
```

### **Scaling & Optimization**
- **Batch Processing:** Adjust batch size (`<batch_size>`) based on memory and performance.
- **Multiple Consumers:** Run several instances of `messenger:consume async` for parallel processing.

---

## API Endpoint for Processed Data

An API endpoint is available to retrieve the processed data with pagination and filtering options. This small API is exposed in

    localhost:9000

### **Endpoint**

```
GET /api/subscribers
```

### **Query Parameters**

- `name` (optional) filters by name (LIKE)
- `email` (optional) filters by email (LIKE)
- `age`(optional) filters by age
- `address`(optional) filters by address (LIKE)
- `page` (optional, default: 1) – Specifies the page number.
- `limit` (optional, default: 50) – Defines how many records per page.


### **Example Requests**

**Get first 100 records:**
```bash
curl -X GET "http://localhost:9000/api/subscribers?limit=100"
```

**Get all records where `age` is `49`:**
```bash
curl -X GET "http://localhost:9000/api/subscribers?age=49"
```

**Paginated response format:**
```json
{
	"data":  [
	{
		"id":  505001,
		"name":  "Sophia Parks",
		"email":  "8t3gIV7Y$!g#T]qGxJ6hKK8!W",
		"age":  40,
		"address":  "1197 Lujfe Drive"
	}
	],
	"totalRecords":  50000,
	"page":  1,
	"limit":  1,
	"totalPages":  50000
}
```

## Future Improvements

### API Architecture Enhancement

The current API implementation follows a basic approach due to its simplicity. However, for future scalability and maintainability, the following architectural improvements could be implemented:

#### Hexagonal Architecture
Converting to a Hexagonal Architecture (also known as Ports and Adapters) would:
- Isolate the domain logic from external concerns
- Make the system more testable
- Allow easier technology changes without affecting business logic
- Provide clearer boundaries between different layers of the application

#### CQRS (Command Query Responsibility Segregation)
Implementing CQRS would be beneficial because:
- It would separate read and write operations, optimizing each path independently
- Read operations could be scaled differently from write operations
- Could implement different storage solutions for reads (optimized for querying) and writes (optimized for updates)
- Would make it easier to implement caching strategies for read operations

This separation would be particularly useful for this system where:
- Write operations (CSV processing) are batch-oriented and heavy
- Read operations (API queries) require quick responses and different optimization strategies

The implementation of these patterns would add some complexity, but the benefits in terms of maintainability, scalability, and flexibility would outweigh the initial development cost for a growing system.


