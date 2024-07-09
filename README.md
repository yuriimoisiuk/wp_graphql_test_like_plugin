# WPGraphQL Test Like Plugin

**Version:** 1.0.0  
**Author:** Y M  
**Requires at least:** 6.1  
**Requires PHP:** 8.1

## Description

Test Like Plugin is a simple WordPress plugin that allows you to add a custom like counter to posts. It integrates with WPGraphQL, enabling you to update and query the like count through GraphQL.

## Features

- Adds a `test_like` meta field to posts.
- Integrates the `test_like` field with WPGraphQL.
- Provides a GraphQL mutation for updating the like count.

## Installation

1. Download the plugin files.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Register the Custom Meta Field

The plugin registers a custom meta field `test_like` for posts. This field is shown in the REST API and can be managed through WordPress's post meta functions.

### WPGraphQL Integration

The plugin extends WPGraphQL to include the `test_like` field in post queries and provides a mutation to update the like count.

### GraphQL Queries and Mutations

#### Query Example

```graphql
query GetPostLikes($id: ID!) {
  post(id: $id) {
    id
    title
    testLike
  }
}
```

## Mutations

## For Mutations use `UpdateTestLike`