# FaceLogin

FaceLogin is a historical PocketMine-MP welcome message plugin. It renders a small text portrait from a joining player's skin and sends it with configurable messages. It does not authenticate players or protect accounts.

## Compatibility and status

The manifest declares PocketMine-MP API 2 and early 3 versions. The source uses old player skin and `AsyncTask` APIs. `SendPlayerFaceTask.php` also uses string offset syntax that fails to parse on the current PHP runtime. It has not been ported or tested on Axolotl-PM 5. Do not install it on a current server.

`resources/messages.yml` contains eight example greeting lines. The task processes skin bytes for display, so a port needs input length validation and tests for current skin formats before handling real players.

The manifest credits Muqsit and includes a Ghast Noob note. Preserve the existing attribution. There is no LICENSE file, CI workflow, DevTools PHAR, Axolotl-PM 5 runtime test or verified current release. Resolve redistribution rights before publishing a modified build.

For questions about the NhanAZ ecosystem, use [NhanAZ Discord](https://discord.gg/j2X83ujT6c).
