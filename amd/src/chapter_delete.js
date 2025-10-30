// This file is part of Moodle - http://moodle.org/
// SPDX-License-Identifier: GPL-3.0-or-later

/* global window */

import {get_string as getString} from 'core/str';
import * as Config from 'core/config';
import Ajax from 'core/ajax';

export const init = () => {
    const selector = '.delete-chapter-btn';
    const root = document;
    root.addEventListener('click', async (e) => {
        const btn = e.target.closest(selector);
        if (!btn) {
            return;
        }
        e.preventDefault();
        const chapterId = btn.getAttribute('data-chapter-id');
        const courseId = btn.getAttribute('data-course-id');
        const blockId = btn.getAttribute('data-block-id');
        const chapterName = btn.getAttribute('data-chapter-name') || '#'+chapterId;
        const prompt = await getString('confirmdeletechapter', 'block_mission_map', chapterName).catch(() =>
            'Delete this chapter and all its missions?');
        if (!window.confirm(prompt)) {
            return;
        }
        Ajax.call([{
            methodname: 'block_mission_map_delete_chapter',
            args: {
                blockid: parseInt(blockId, 10),
                courseid: parseInt(courseId, 10),
                chapterid: parseInt(chapterId, 10)
            }
        }])[0].then(() => {
            window.location.reload();
        }).catch(() => {
            window.location.reload();
        });
    }, true);
};

export default {init};


