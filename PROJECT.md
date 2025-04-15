# CrediCarbSf

## Description
Application for project and company management with certification processes, implemented in a clean, scalable architecture.

## Technical Requirements
- PHP 8.2
- Symfony 6.4
- MySQL
- Docker environment
- Composer

## Architecture
This project follows a Hexagonal Architecture with Domain-Driven Design principles. All code adheres to SOLID principles and Clean Code practices. The system uses Events and Subscriber patterns for communication between components.

The project is currently being enhanced with CQRS+Event Sourcing, implemented incrementally.

### Directory Structure
- `/src/Domain`: Domain entities, Value Objects and Aggregates
- `/src/Application`: Commands and Queries (CQRS)
- `/src/Infrastructure`: Repositories, adapters and infrastructure-related services

## Core Entities
for the moment
- **Project**: Central entity representing projects
- **Company**: Entities representing companies that own or buy projects
- **Certification**: Represents certifications for projects
- **CertificationAuthority**: Entities that issue certifications
- **CertificationTypeEntity**: Types of certifications available
- **Discount**: Discount information applied to purchases
- **ShoppingCart**: Shopping cart for purchasing projects
- **ShoppingCartItem**: Individual items in shopping carts
- **User**: User accounts in the system

## Testing Approach
The project follows extensive test coverage:
- Unit tests for domain logic
- Integration tests for application services
- Functional tests for API endpoints
