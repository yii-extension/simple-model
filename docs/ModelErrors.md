# ModelErrors

It is a class that contains a collection of methods, which allow to handle properly the errors of the model instance.

## Reference

|Method | Description |
|-------|-------------|
`add(string $attribute, string $error)` | adds an error to the specified attribute.
`addErrors(array $values)` | adds the errors for model instance.
`clear(?string $attribute = null)` | removes errors for the specified attribute, or attribute is `null` remove all attributes.
`get(string $attribute)` | returns the error message for the specified attribute.
`getAll()` | returns all errors.
`getFirst(string $attribute)` | returns the first error message for the specified attribute.
`getFirsts()` | returns the first error message for all attributes.
`getSummary()` |  returns errors for all attributes as a one|dimensional array.
`getSummaryFirst()` | returns the first error message for all attributes as a one|dimensional array.
`has(?string $attribute = null)` | returns a value indicating whether there is any validation error, use `null` to check all attributes.
