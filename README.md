# jikanwari

## 概要
- jikanwariブロックは Moodle のブロックプラグインです。日本でよく見られる形の時間割をダッシュボードに表示・管理することができます。
- Moodle 4.5とMoodle 5.1で動作確認済み（20260106）

主な機能
- ダッシュボードに時間割を表示できます。
- 各コマに自分が登録されているコースを割り当てることができます。
- 設定されたコース名をカスタマイズして分かりやすい名称で時間割に表示することができます。
- 各コマにメモを追記できます。
- 一括リセット機能を有します。
- 管理画面からコマ数や曜日の表示設定を変更できます。

インストール（Moodle のブロックとして）
1. リポジトリをcloneするかダウンロードしてください。
2. Moodle のルートディレクトリにある `blocks` ディレクトリへ本リポジトリを`jikanwari`として配置します。
   - 例: `moodle/blocks/jikanwari` のように配置します。
3. Moodle サイト管理にログインし、`サイト管理 > 通知` (Site administration > Notifications) にアクセスします。
   - プラグインのインストール・データベース更新処理が自動的に実行されます。


## Overview
The **jikanwari** block is a Moodle block plugin designed to display and manage a Japanese-style school timetable on the user's dashboard.

## Key Features
* **Dashboard Timetable:** View your weekly schedule at a glance on the Moodle dashboard.
* **Course Assignment:** Assign your enrolled courses to specific time slots.
* **Custom Display Names:** Rename courses on the timetable for better clarity and personal organization.
* **Slot Comments:** Add notes or comments to individual time slots.
* **Bulk Reset:** Includes a feature to clear all timetable data in one go.
* **Flexible Configuration:** Administrators can customize the number of periods and the days of the week shown through the settings page.

## Installation (As a Moodle block)
1.  Download or clone this repository.
2.  Place the entire folder into the `blocks/` directory of your Moodle root installation.
    * Example: `moodle/blocks/jikanwari`
3.  Log in to your Moodle site as an administrator.
4.  Navigate to `Site administration > Notifications`.
    * The system will automatically detect the new plugin and guide you through the installation and database update process.

