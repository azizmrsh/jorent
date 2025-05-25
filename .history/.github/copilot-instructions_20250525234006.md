# COPILOT EDITS OPERATIONAL GUIDELINES

## PRIME DIRECTIVE

* Avoid working on more than one file at a time.
* Multiple simultaneous edits to a file will cause corruption.
* Be chatting and teach about what you are doing while coding.

## LARGE FILE & COMPLEX CHANGE PROTOCOL

### MANDATORY PLANNING PHASE

When working with large files (>300 lines) or complex changes:

1. **ALWAYS start by creating a detailed plan BEFORE making any edits**

2. Your plan **MUST include**:

   * All functions/sections that need modification
   * The order in which changes should be applied
   * Dependencies between changes
   * Estimated number of separate edits required

3. Format your plan as:

```md
## PROPOSED EDIT PLAN
Working with: [filename]
Total planned edits: [number]
```

### MAKING EDITS

* Focus on one conceptual change at a time
* Show clear "before" and "after" snippets when proposing changes
* Include concise explanations of what changed and why
* Always check if the edit maintains the project's coding style

### Edit sequence:

1. \[First specific change] - Purpose: \[why]
2. \[Second specific change] - Purpose: \[why]
3. Ask: "Do you approve this plan? I'll proceed with Edit \[number] after your confirmation."
4. **WAIT for explicit user confirmation** before making ANY edits

### EXECUTION PHASE

* After each individual edit, clearly indicate progress:

  * "✅ Completed edit \[#] of \[total]. Ready for next edit?"
* If additional needed changes are discovered:

  * **STOP**, update the plan
  * Get approval before continuing

## REFACTORING GUIDANCE

* Break work into logical, independently functional chunks
* Ensure each intermediate state maintains functionality
* Consider temporary duplication as a valid interim step
* Always indicate the refactoring pattern being applied

## RATE LIMIT AVOIDANCE

* For very large files, suggest splitting changes across multiple sessions
* Prioritize changes that are logically complete units
* Always provide clear stopping points

## WINDOWS POWERSHELL COMPATIBILITY

* Assume usage of PowerShell terminal
* Provide all command-line instructions using PowerShell-compatible syntax
* Avoid using Linux/macOS-specific commands (`rm`, `touch`, etc.)
* Use `Remove-Item`, `New-Item`, `Set-Content`, etc. instead

## GENERAL REQUIREMENTS

Use modern technologies and prioritize clean, maintainable, well-documented code.

### Accessibility

* Ensure **WCAG 2.1** AA (AAA where feasible)
* Add:

  * Labels for all form fields
  * Proper **ARIA** roles and attributes
  * Adequate color contrast
  * Alternative texts (`alt`, `aria-label`)
  * Semantic HTML structure
  * Audit tools like **Lighthouse**

### Browser Compatibility

* Support latest 2 versions of: Chrome, Firefox, Edge, Safari
* Feature detection (`if ('fetch' in window)`) > user-agent sniffing
* Progressive enhancement using **polyfills** and **bundlers** (e.g., **Vite**)

### PHP REQUIREMENTS

* **Version**: PHP 8.1+
* Use:

  * Named arguments, union types, nullable types
  * Match expressions, nullsafe operator (`?->`)
  * Attributes instead of annotations
  * Typed and readonly properties
  * Enumerations (`enum`)
  * Constructor property promotion
* **Standards**:

  * `declare(strict_types=1);`
  * PSR-12 code style
  * Dependency Injection > Inheritance
  * PHPDoc blocks for **PHPStan/Psalm**
  * Exception-based error handling with clear messages

### HTML/CSS

**HTML**:

* Use semantic HTML5 elements
* ARIA roles for accessibility
* Valid W3C markup
* Responsive design
* Lazy loading (`loading="lazy"`)
* Modern image formats (`WebP`, `AVIF`)
* `srcset`/`sizes` for responsive images
* SEO elements (`<title>`, `<meta>`, OG tags)

**CSS**:

* Modern layout: Flexbox, CSS Grid
* CSS variables
* Animations & transitions
* Media queries
* Logical properties (`margin-block`, etc.)
* Selectors: `:is()`, `:has()`
* BEM naming or similar
* CSS nesting where applicable
* Dark mode via `prefers-color-scheme`
* Variable fonts, modern units (`rem`, `vh`, `vw`)

### JavaScript

**Target**: ES2020+
**Use**:

* Arrow functions, template literals
* Destructuring, spread/rest
* Async/await
* Optional chaining (`?.`), nullish coalescing (`??`)
* Classes, private fields
* Dynamic imports, `Promise.allSettled()`
* Avoid `var`, jQuery, callbacks
* Modular code with ES Modules

**Error Handling**:

* `try-catch` blocks
* Differentiate between:

  * Network errors
  * Business logic errors
  * Runtime exceptions
* Friendly user messages, dev logs
* Global error handlers (`window.addEventListener('unhandledrejection')`)

### FILE/FOLDER STRUCTURE

```
project-root/
├── api/
├── config/
├── data/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   ├── fonts/
│   └── index.html
├── src/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── utilities/
├── tests/
├── docs/
├── logs/
├── scripts/
└── temp/
```

### DOCUMENTATION

* JSDoc for JS/TS
* PHPDoc for PHP
* Markdown for external docs
* Include: `@param`, `@return`, `@throws`, `@author`

### DATABASE (SQLite 3.46+ Recommended)

* Use:

  * JSON columns
  * Strict mode
  * Generated columns
  * Foreign keys
  * Check constraints
  * Transactions

### SECURITY

* Input sanitization & validation
* Parameterized queries
* CSRF protection
* Secure cookies (`HttpOnly`, `Secure`, `SameSite=Strict`)
* Role-based access control
* CSP headers
* Internal logging/monitoring
