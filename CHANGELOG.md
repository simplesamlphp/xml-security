# Changelog

All notable changes to this project are documented in this file.

## v3.1.0

This release introduces the extension points needed to perform RSA signing and
RSA key-transport decryption through a remote
[Private Key Agent (PKA)](https://github.com/OpenConext/OpenConext-private-key-agent),
so that private keys are never loaded into the PHP process on those paths. No
existing public method signatures changed, but see the behavior change below.

### ⚠️ Behavior change on the key-transport decryption path

`EncryptedKey::decrypt()` now **reads and enforces** the `<ds:DigestMethod>` and
`<xenc11:MGF>` children of the `<xenc:EncryptionMethod>` element. Previously these
children were ignored:

- A non-SHA-1 digest/MGF is now either delegated to the PKA backend or
  explicitly rejected with `UnsupportedAlgorithmException` (local `OpenSSL`
  backend), instead of silently weakening to SHA-1 or failing with an opaque
  OpenSSL error.
- Peers that today send inconsistent-but-ignored digest/MGF children on
  otherwise-working SHA-1 ciphertext will now get an explicit error.

### Added

- **Private Key Agent backends** (`SimpleSAML\XMLSecurity\Backend\PrivateKeyAgent\`):
  - `PrivateKeyAgentSignatureBackend` (implements `SignatureBackend`), hashes
    locally and delegates the raw RSA signature to the agent; `verify()` runs
    locally.
  - `PrivateKeyAgentEncryptionBackend` (implements `EncryptionBackend` and the new
    `OAEPParametersAware`), delegates RSA key-transport decryption to the agent;
    `encrypt()` runs locally. The agent's algorithms couple the OAEP digest and
    MGF1 hash one-to-one, so only matched pairs are supported and the default for
    a parameterless `xmlenc11#rsa-oaep` deviates from the W3C default of SHA-1,
    see "Operational requirements and limitations" in the README before deploying.
  - Supporting contracts and helpers: `TokenProvider`, `KeyNameResolver`,
    `StaticKeyNameResolver`, `FingerprintKeyNameResolver`, and the internal
    `AlgorithmMap` / `PrivateKeyAgentHttpClient`.
- **Capability interface** `SimpleSAML\XMLSecurity\Backend\OAEPParametersAware`
  (`setOAEPParams(?string $digestAlg, ?string $mgf)`) to carry OAEP digest/MGF to
  backends without changing the `EncryptionBackend` interface. Implemented by
  `OpenSSL`, `PrivateKeyAgentEncryptionBackend`, and `AbstractKeyTransporter`.
- **Algorithm-factory closure registration**
  `SignatureAlgorithmFactory::registerAlgorithmFactory()` and
  `KeyTransportAlgorithmFactory::registerAlgorithmFactory()`, register a
  `\Closure(KeyInterface, string): AlgorithmInterface`. The existing blacklist
  applies unchanged; `registerAlgorithm()` is untouched.
- **Registrable algorithm wrappers** `Alg\Signature\PrivateKeyAgentRSA` and
  `Alg\KeyTransport\PrivateKeyAgentRSA` with deterministic key-type routing:
  an `X509Certificate` routes to the agent backend, an `AsymmetricKey`
  (`PrivateKey`/`PublicKey`) keeps the local `OpenSSL` backend, any other
  `KeyInterface` fails closed. Routing is never a fallback-on-error. The injected
  PKA backend is treated as a prototype: each wrapper clones it, so one backend
  registered at boot safely serves any number of concurrent algorithm instances.
- A dedicated marker interface `Exception\PrivateKeyAgentExceptionInterface` and
  the agent-interaction exceptions `AuthenticationException`,
  `AuthorizationException`, `UnknownKeyException`, `AgentUnavailableException`,
  `AgentProtocolException`, `InvalidRequestException`, `MissingTokenException`.
- New hard dependencies for the HTTP transport: `psr/http-client`,
  `psr/http-message`, `psr/http-factory` (PSR-18/PSR-17, injected via the
  constructor; no auto-discovery).
- New hard dependency on the `ext-filter` PHP extension. It is required by
  `PrivateKeyAgentHttpClient`, which uses `filter_var()` with
  `FILTER_VALIDATE_IP`/`FILTER_FLAG_IPV4` to determine whether the configured
  agent URL points at a loopback address, so that plain HTTP is only accepted
  for local agents. `ext-filter` is enabled by default in PHP, but is now
  declared explicitly because the library depends on it.

### Changed

- `OpenSSL` now implements `OAEPParametersAware` and fails closed with
  `UnsupportedAlgorithmException` on any non-SHA-1 OAEP digest/MGF (on all PHP
  versions), instead of silently using SHA-1.

### Security notes

- **Insecure algorithms stay blocked by default.** SHA-1 signing
  (`SIG_RSA_SHA1`) and RSA v1.5 key transport (`KEY_TRANSPORT_RSA_1_5`) remain in
  each factory's `DEFAULT_BLACKLIST`. Because the PKA wrappers are only built via
  `getAlgorithm()`, a blacklisted algorithm throws `BlacklistedAlgorithmException`
  before any backend is reached. Unblocking them requires an explicit,
  operator-configured blacklist on the factory.
- **Transport security.** The agent base URL must be `https://`. Plain `http://`
  is only allowed via the explicit `allowInsecureHttp: true` constructor flag and
  is then further restricted to loopback hosts (`localhost`, `127.0.0.0/8`, `::1`),
  so the bearer token can never leave the host in cleartext. Both checks are
  validated fail-closed at construction.
- Bearer tokens never appear in exception messages or logs; token parameters
  carry `#[\SensitiveParameter]`.
- **No fallback.** When the agent is unreachable or fails, the operation raises a
  controlled exception and never falls back to a local private-key operation.
