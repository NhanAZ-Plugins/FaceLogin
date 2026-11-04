# FaceLogin

Player skin portrait welcome plugin for Axolotl-PM API 5.

[![CI](https://github.com/NhanAZ-Plugins/FaceLogin/actions/workflows/build.yml/badge.svg)](https://github.com/NhanAZ-Plugins/FaceLogin/actions/workflows/build.yml)
[![Latest release](https://img.shields.io/github/v/release/NhanAZ-Plugins/FaceLogin)](https://github.com/NhanAZ-Plugins/FaceLogin/releases/latest)
[![License](https://img.shields.io/github/license/NhanAZ-Plugins/FaceLogin)](https://github.com/NhanAZ-Plugins/FaceLogin/blob/main/LICENSE)
[![Discord](https://img.shields.io/discord/986553214889517088?label=Discord&logo=discord&logoColor=white&color=5865F2)](https://discord.gg/j2X83ujT6c)


## Compatibility

- Axolotl-PM API minimum: `5.0.0`.
- The CI build and PHPStan checks use the Axolotl-PM API 5.0.0 source revision [`b16cc69`](https://github.com/axolotl-pm/PocketMine-MP/commit/b16cc69974d4e805d21e6328ab77c0e17cd7116d) and API 5.51.1 source revision [`e881fc1`](https://github.com/axolotl-pm/PocketMine-MP/commit/e881fc1bc1c401545baf6bd1f824e41ff3f57bd1).
- Runtime startup is checked with the official Axolotl-PM 5.51.1 server PHAR. This is a point-in-time smoke test, not a claim that every intervening 5.x version or a connected player's rendering has been tested.
- PHP 8.1 or newer is required.

The manifest declares API `5.0.0`. That is the minimum API requirement; CI records exact source revisions for repeatability. See the [Build and validate FaceLogin workflow](https://github.com/NhanAZ-Plugins/FaceLogin/actions/workflows/build.yml) for current results.

## Installation

Download `FaceLogin.phar` from the [latest stable release](https://github.com/NhanAZ-Plugins/FaceLogin/releases/latest) and place it in the server's `plugins/` directory. Restart Axolotl-PM to create the default `plugin_data/FaceLogin/messages.yml` configuration.

The `facelogin.show` permission defaults to `true`. Set it to `false` for players who should not receive the portrait.

## Configuration

The `messages` mapping in `messages.yml` supports lines 1 through 8. Values must be strings. `{NAME}` is replaced with the joining player's name. Lines outside that range and invalid values are ignored. A skin must use one of the recognized 64x32, 64x64, or 128x128 RGBA image sizes to render.

The committed sample keeps its masked server-specific values. Replace them in the generated plugin configuration with your own welcome text.

## Build and checks

The GitHub Actions workflow uses the [DevTools](https://github.com/NhanAZ/DevTools) reusable workflow to run PHPStan at maximum level, build and inspect the plugin PHAR, and upload the verified artifact. It checks both pinned Axolotl-PM API 5 source revisions listed above. A separate startup smoke test uses the exact PHAR produced by CI and verifies that the plugin enables and creates its configuration.

## Attribution and license

This plugin is based on [Muqsit's FaceLogin](https://github.com/Muqsit/FaceLogin/tree/71ee8a9b5384a17d180b75307fa4de1874f5f525). The original face-rendering implementation is credited to Legoboy0215 in the source. Original copyright and license notices are retained. The upstream repository's `LICENSE.md` is included. The existing sample configuration also retains its Ghast Noob attribution note.
