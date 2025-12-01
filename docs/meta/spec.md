# Collections — Package Specification

> **Cluster:** `data`
> **Language:** `php`
> **Milestone:** `m2`
> **Repo:** `https://github.com/decodelabs/collections`
> **Role:** Data structures

This document describes the purpose, contracts, and design of **Collections** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** Collections in their own applications or libraries.
- Contributors **maintaining or extending** Collections.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

Collections provides reusable collection components for PHP, offering a comprehensive set of data structures optimized for functionality over speed. The package provides mutable and immutable implementations of sequences (ordered lists), dictionaries (key-value maps), and trees (nested hierarchical structures). It serves as a foundation for more complex data structures and provides a rich API for collection manipulation, transformation, and querying.

### 1.2 Non-Goals

Collections does **not**:

- Provide high-performance data structures optimized for speed — it prioritizes functionality
- Replace PHP's native array type — it complements it with object-oriented interfaces
- Provide database-like querying or indexing — it focuses on in-memory collection operations
- Handle serialization or persistence — it's for in-memory data structures
- Provide thread-safe or concurrent collections — it's designed for single-threaded use
- Match the performance of the PHP Ds extension — users should use Ds for performance-critical code

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `data` (see Chorus taxonomy)
- Collections is a foundational data structures package that provides reusable collection components for the Decode Labs ecosystem. It sits in the data cluster alongside other data management packages. It depends on Coercion, Exceptional, Fluidity, and Lucid, and is used extensively throughout the ecosystem for structured data manipulation.

### 2.2 Typical Usage Contexts

Typical places Collections appears:

- Configuration data structures
- Form data handling
- API request/response processing
- Data transformation pipelines
- Tree-structured data (e.g., nested configurations, hierarchical menus)
- Collection manipulation and filtering
- Data validation and sanitization contexts

Collections is intended to be used whenever code needs structured, object-oriented collection types with rich manipulation APIs, especially when functionality and ease of use are more important than raw performance.

---

## 3. Public Surface

> This section focuses on the conceptual API, not every symbol.

### 3.1 Key Types

The primary public types are:

- `DecodeLabs\Collections\Collection`
  Base interface for all collections. Extends `ArrayProvider`, `JsonSerializable`, `Countable`, `IteratorAggregate`, and `Then` (from Fluidity). Provides common collection operations like filtering, mapping, reducing, and set operations.

- `DecodeLabs\Collections\SequenceInterface`
  Interface for ordered sequences (lists) with integer keys. Extends `Collection` and `Sortable`. Provides sequence-specific operations like padding, slicing, and range creation.

- `DecodeLabs\Collections\Sequence`
  Mutable sequence implementation. Implements `SequenceInterface`, `ArrayAccess`, and `IteratorAggregate`.

- `DecodeLabs\Collections\ImmutableSequence`
  Immutable sequence implementation. Same interface as `Sequence` but operations return new instances.

- `DecodeLabs\Collections\DictionaryInterface`
  Interface for key-value dictionaries (maps). Extends `MapInterface`. Provides dictionary-specific operations.

- `DecodeLabs\Collections\Dictionary`
  Mutable dictionary implementation. Implements `DictionaryInterface`, `ArrayAccess`, and `IteratorAggregate`.

- `DecodeLabs\Collections\ImmutableDictionary`
  Immutable dictionary implementation. Same interface as `Dictionary` but operations return new instances.

- `DecodeLabs\Collections\MapInterface`
  Base interface for map-like structures. Extends `Collection` and `Sortable`. Provides map operations like key/value manipulation, merging, and flipping.

- `DecodeLabs\Collections\TreeInterface`
  Interface for tree structures (nested hierarchical data). Extends `MapInterface`, `ValueProvider`, and `LucidProvider`. Provides tree-specific operations like node access, value management, and delimited string parsing.

- `DecodeLabs\Collections\Tree`
  Mutable tree implementation. Supports nested structures with dot-notation key access and value storage at nodes.

- `DecodeLabs\Collections\Sortable`
  Interface for sortable collections. Provides various sorting methods (by key, by value, natural, case-insensitive, etc.).

- `DecodeLabs\Collections\ArrayProvider`
  Interface for types that can be converted to arrays.

- `DecodeLabs\Collections\ValueProvider`
  Interface for types that provide a value (used by Tree).

- `DecodeLabs\Collections\AttributeContainer`
  Interface/trait for containers that can store attributes.

### 3.2 Main Entry Points

The main usage pattern is through concrete implementations:

```php
use DecodeLabs\Collections\Sequence;
use DecodeLabs\Collections\Dictionary;
use DecodeLabs\Collections\Tree;

$sequence = new Sequence([1, 2, 3]);
$dictionary = new Dictionary(['key' => 'value']);
$tree = new Tree(['nested' => ['key' => 'value']]);
```

---

## 4. Dependencies

### 4.1 Decode Labs

- `decodelabs/coercion` (required)
  Used for type coercion throughout collection operations.

- `decodelabs/exceptional` (required)
  Used for exception handling when operations fail.

- `decodelabs/fluidity` (required)
  Used for fluent interface support via the `Then` interface.

- `decodelabs/lucid` (required)
  Used for value sanitization and validation. Tree implements `LucidProvider` for integration with Lucid.

### 4.2 External

- None

### 4.3 Optional Integrations

- None

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- Mutable collections (`Sequence`, `Dictionary`) modify themselves and return `$this`
- Immutable collections (`ImmutableSequence`, `ImmutableDictionary`) return new instances
- All collections implement `ArrayAccess` for array-like access
- All collections implement `IteratorAggregate` for iteration
- All collections implement `Countable` for `count()` support
- All collections implement `JsonSerializable` for JSON encoding
- Tree nodes can have both values and children simultaneously
- Tree supports dot-notation key access (e.g., `tree['parent.child']`)
- Sequence keys are always integers (0-based)
- Dictionary keys can be integers or strings

### 5.2 Input & Output Contracts

**Collection Operations:**
- `isEmpty(): bool` — Returns true if collection has no elements
- `isMutable(): bool` — Returns true if collection is mutable
- `copy(): static` — Returns a copy of the collection
- `getFirst(?callable $filter): mixed` — Returns first element, optionally filtered
- `getLast(?callable $filter): mixed` — Returns last element, optionally filtered
- `contains(mixed $value, bool $strict): bool` — Checks if value exists
- `containsRecursive(mixed $value, bool $strict): bool` — Recursive contains check
- `slice(int $offset, ?int $length): static` — Returns slice of collection
- `chunk(int $size): array` — Splits collection into chunks
- `filter(?callable $callback): static` — Filters collection elements
- `map(callable $callback, iterable ...$arrays): static` — Maps elements
- `reduce(callable $callback, mixed $initial): mixed` — Reduces collection to single value
- `getSum(?callable $filter): float` — Sums numeric values
- `getProduct(?callable $filter): float` — Multiplies numeric values
- `getAvg(?callable $filter): ?float` — Calculates average

**Sequence Operations:**
- `get(int $key): mixed` — Gets value at index
- `set(int $key, mixed $value): static` — Sets value at index
- `has(int ...$keys): bool` — Checks if keys exist
- `remove(int ...$keys): static` — Removes keys
- `keep(int ...$keys): static` — Keeps only specified keys
- `fill(mixed $value): static` — Fills all positions with value
- `padLeft(int $size, mixed $value): static` — Pads left side
- `padRight(int $size, mixed $value): static` — Pads right side
- `collapse(bool $unique, bool $removeNull): static` — Flattens nested arrays
- `unique(int $flags): static` — Removes duplicates
- `createRange(int $start, int $end, int $step): static` — Creates range sequence

**Dictionary/Map Operations:**
- `get(mixed $key): mixed` — Gets value by key
- `set(mixed $key, mixed $value): static` — Sets value by key
- `pull(mixed $key): mixed` — Gets and removes value
- `has(mixed ...$keys): bool` — Checks if keys exist
- `hasKey(mixed ...$keys): bool` — Checks if keys exist (alias)
- `remove(mixed ...$keys): static` — Removes keys
- `keep(mixed ...$keys): static` — Keeps only specified keys
- `findKey(mixed $value, bool $strict): mixed` — Finds key by value
- `flip(): MapInterface` — Swaps keys and values
- `changeKeyCase(int $case): static` — Changes key case
- `combineWithKeys(iterable $keys): static` — Combines with keys
- `combineWithValues(iterable $values): static` — Combines with values

**Tree Operations:**
- `getNode(int|string $key): static` — Gets node (supports dot notation)
- `setNode(int|string $key, mixed $value): static` — Sets node value
- `hasNode(int|string ...$keys): bool` — Checks if nodes exist
- `getValue(): mixed` — Gets node value
- `setValue(mixed $value): static` — Sets node value
- `hasValue(): bool` — Checks if node has value
- `hasAnyValue(): bool` — Checks if node or children have values
- `removeEmpty(): static` — Removes empty nodes
- `fromDelimitedString(string $string, string $setDelimiter, string $valueDelimiter): TreeInterface` — Parses delimited string
- `toDelimitedString(string $setDelimiter, string $valueDelimiter): string` — Converts to delimited string

**Sortable Operations:**
- `sort(int $flags): static` — Sorts by value (preserves keys)
- `reverseSort(int $flags): static` — Reverse sorts by value
- `sortBy(callable $callback): static` — Sorts using callback
- `sortValues(int $flags): static` — Sorts by value (reindexes)
- `sortKeys(int $flags): static` — Sorts by key
- `reverse(): static` — Reverses order
- `shuffle(): static` — Shuffles elements

### 5.3 Set Operations

Collections provide comprehensive set operations:
- `diffAssoc`, `diffValues`, `diffKeys` — Difference operations
- `intersectAssoc`, `intersectValues`, `intersectKeys` — Intersection operations
- All support callback variants (`*By`, `*ByValue`, `*All`) for custom comparison

### 5.4 Tree-Specific Behaviours

- Tree nodes can store values directly (via `setValue()`)
- Tree nodes can have children (nested structure)
- Dot-notation keys (e.g., `'parent.child'`) automatically create nested structure
- Tree implements `LucidProvider` for value sanitization
- Tree supports delimited string parsing (e.g., query strings, form data)

---

## 6. Error Handling

- Mutable collections throw exceptions when attempting to modify immutable collections
- `get()` methods return `null` for missing keys (no exceptions)
- `as*` style operations (if any) would throw exceptions, but Collections primarily uses nullable returns
- Invalid operations (e.g., combining mismatched key/value counts) throw `Exceptional::InvalidArgument`
- Tree parsing throws `Exceptional::UnexpectedValue` for invalid delimited strings
- Array access on immutable collections may throw exceptions for write operations

---

## 7. Configuration & Extensibility

- Collections are not configurable — behaviour is fixed
- Immutability is controlled by the `Mutable` constant in concrete classes
- Tree key separator is fixed to `'.'` (dot) via `KeySeparator` constant
- Custom collection types can be created by implementing interfaces or extending traits
- PHPStan extensions are provided for better static analysis support

---

## 8. Interactions with Other Packages

### 8.1 Coercion

Collections uses Coercion extensively for type conversion when working with mixed values, especially in Tree operations and value extraction.

### 8.2 Exceptional

Collections uses Exceptional for all exception handling, providing consistent error reporting across the ecosystem.

### 8.3 Fluidity

Collections implements Fluidity's `Then` interface, enabling fluent method chaining throughout the API.

### 8.4 Lucid

Tree implements `LucidProvider`, allowing Tree nodes to be used as value sources in Lucid sanitization and validation contexts. This enables Tree to participate in Lucid's validation pipeline.

---

## 9. Usage Examples

### 9.1 Basic Sequence Operations

```php
use DecodeLabs\Collections\Sequence;

$seq = new Sequence([1, 2, 3, 4, 5]);
$filtered = $seq->filter(fn($v) => $v > 2); // [3, 4, 5]
$mapped = $seq->map(fn($v) => $v * 2); // [2, 4, 6, 8, 10]
$sum = $seq->getSum(); // 15
```

### 9.2 Dictionary Operations

```php
use DecodeLabs\Collections\Dictionary;

$dict = new Dictionary(['a' => 1, 'b' => 2, 'c' => 3]);
$value = $dict->get('a'); // 1
$dict->set('d', 4);
$has = $dict->has('a', 'b'); // true
$flipped = $dict->flip(); // [1 => 'a', 2 => 'b', 3 => 'c']
```

### 9.3 Tree Operations

```php
use DecodeLabs\Collections\Tree;

$tree = new Tree();
$tree->setNode('parent.child', 'value');
$value = $tree->get('parent.child'); // 'value'
$node = $tree->getNode('parent'); // Tree node

// From delimited string
$tree = Tree::fromDelimitedString('key=value&nested[key]=value');
```

### 9.4 Immutable Collections

```php
use DecodeLabs\Collections\ImmutableSequence;

$seq = new ImmutableSequence([1, 2, 3]);
$newSeq = $seq->push(4); // Returns new instance, original unchanged
```

### 9.5 Set Operations

```php
use DecodeLabs\Collections\Sequence;

$seq1 = new Sequence([1, 2, 3, 4]);
$seq2 = new Sequence([3, 4, 5, 6]);
$diff = $seq1->diffValues($seq2); // [1, 2]
$intersect = $seq1->intersectValues($seq2); // [3, 4]
```

---

## 10. Implementation Notes (for Contributors)

### 10.1 Mutability Control

Mutability is controlled by a `Mutable` class constant:
- `Mutable = true` — Operations modify the instance
- `Mutable = false` — Operations return new instances

The trait implementations check this constant to determine behaviour.

### 10.2 Trait-Based Implementation

Collections use traits (`CollectionTrait`, `SequenceTrait`, `DictionaryTrait`) to share implementation between mutable and immutable variants. The traits check the `Mutable` constant to determine whether to modify `$this` or return a new instance.

### 10.3 Tree Structure

Tree implements a nested structure where:
- Each node can have a value (`$value` property)
- Each node can have children (`$items` array of Tree instances)
- Dot-notation keys are split and traversed automatically
- Empty nodes (no value, no children) are considered empty

### 10.4 Array Access

All collections implement `ArrayAccess`:
- `offsetGet()` — Gets value by key
- `offsetSet()` — Sets value by key
- `offsetExists()` — Checks if key exists
- `offsetUnset()` — Removes key

For immutable collections, `offsetSet()` and `offsetUnset()` may throw exceptions.

### 10.5 PHPStan Integration

Collections includes PHPStan extensions (`TreeReflectionExtension`) to improve static analysis of generic types and tree structures.

### 10.6 Delimited String Parsing

Tree supports parsing delimited strings (like query strings):
- `setDelimiter` — Separates key-value pairs (default: `'&'`)
- `valueDelimiter` — Separates key from value (default: `'='`)
- Supports URL encoding/decoding
- Supports bracket notation for nested structures (e.g., `key[subkey]=value`)

---

## 11. Testing & Quality

- **Code Quality Score:** 3/5
- **README Quality Score:** 1/5
- **Documentation Score:** 0/5 (this spec)
- **Test Coverage Score:** 0/5

See `composer.json` for supported PHP versions.

---

## 12. Roadmap & Future Ideas

- Improve performance optimizations
- Add more collection types (e.g., Set, Queue, Stack)
- Enhanced documentation and usage examples
- Add test coverage
- Consider adding lazy evaluation for large collections
- Add support for collection serialization
- Consider adding collection comparison operations

---

## 13. References

- [Coercion Package](https://github.com/decodelabs/coercion) — Type coercion
- [Fluidity Package](https://github.com/decodelabs/fluidity) — Fluent interfaces
- [Lucid Package](https://github.com/decodelabs/lucid) — Value sanitization
- [PHP Ds Extension](https://www.php.net/manual/en/book.ds.php) — High-performance data structures
- [Chorus Package Index](../../../chorus/config/packages.json) — Ecosystem metadata

