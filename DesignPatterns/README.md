## Description

Product examples with implementation Design Patterns. Automation tests is included.


### Creational Patterns

 Status | Pattern                                        | Description                                                                                                 |
|--------|:-----------------------------------------------|:------------------------------------------------------------------------------------------------------------|
| Done   | [FactoryMethod](Creational/FactoryMethod)      | Sending notifications via different channels: Email or SMS                                                  |
| Done   | [AbstractFactory](Creational/AbstractFactory)  | Implementation of notification components: email, SMS, templates. For different regulatory zones: EU, US... |
| Done   | [Builder](Creational/Builder)                  | Orders for e-commerce                                                                                       |
| Done   | [Prototype](Creational/Prototype)              | Generating Invoice                                                                                          |
| Done   | [Singleton](Creational/Singleton)              | Logging events: errors, user actions, system events                                                         |

### Structural Patterns

 Status     | Pattern                           | Description                                                                                                                     |
|------------|:----------------------------------|:--------------------------------------------------------------------------------------------------------------------------------|
| -          | Adapter                           | -                                                                                                                               |
| -          | Bridge                            | -                                                                                                                               |
| -          | Composite                         | -                                                                                                                               |
| In process | [Decorator](Structural/Decorator) | Secure document storage with virus scanning and audit logging layered around an S3 storage service.                             |
| Done       | [Facade](Structural/Facade)       | The single CheckoutFacade point hides the payment gateway, warehouse reservation, delivery, invoicing, anti-fraud, and logging. |
| -          | Flyweight                         | -                                                                                                                               |
| -          | Proxy                             | -                                                                                                                               |


### Behavioral Patterns

 Status     | Pattern                                         | Description                                                   |
|------------|:------------------------------------------------|:--------------------------------------------------------------|
| -          | Chain of Responsibility                         | -                                                             |
| -          | Command                                         | -                                                             |
| -          | Iterator                                        | -                                                             |
| In process | [Mediator](Behavioral/Mediator)                 | -                                                             |
| Done       | [Memento](Behavioral/Memento)                   | Document editor with versioning, undo/redo and storage limits |
| -          | Observer                                        | -                                                             |
| -          | State                                           | -                                                             |
| -          | Strategy                                        | -                                                             |
| -          | Template Method                                 | -                                                             |
| -          | Visitor                                         | -                                                             |