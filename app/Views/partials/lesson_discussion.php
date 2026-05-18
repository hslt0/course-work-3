<div id="discussion" class="mt-12 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-8 border-b border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            Lesson Discussion
        </h2>
        <p class="mt-1 text-sm text-gray-500">Ask questions or share your thoughts with other students.</p>
    </div>
    
    <div class="p-8 bg-gray-50 border-b border-gray-100">
        <form action="<?= URLROOT ?>/lessons/comment/<?= $lesson->id ?>" method="POST">
            <label for="comment" class="sr-only">Your comment</label>
            <textarea id="comment" name="comment" rows="3" required class="block w-full border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-4 py-3 border shadow-sm" placeholder="Write a comment..."></textarea>
            <div class="mt-3 flex justify-end">
                <button type="submit" class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm focus:outline-none transition-colors">
                    Post Comment
                </button>
            </div>
        </form>
    </div>

    <div class="p-8">
        <?php if (empty($comments)): ?>
            <div class="text-center py-6">
                <p class="text-gray-500 italic">No comments yet. Be the first to start the discussion!</p>
            </div>
        <?php else: ?>
            <ul class="space-y-6">
                <?php foreach ($comments as $comment): ?>
                    <li class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold uppercase">
                                <?= substr($comment->user_name, 0, 1) ?>
                            </div>
                        </div>
                        <div class="flex-grow bg-gray-50 rounded-2xl p-4 border border-gray-100 relative group">
                            
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="<?= URLROOT ?>/admin/delete_comment/<?= $comment->id ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                        <input type="hidden" name="lesson_id" value="<?= $lesson->id ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Comment">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>

                            <div class="flex items-center justify-between mb-2 pr-6">
                                <h4 class="text-sm font-bold text-gray-900"><?= htmlspecialchars($comment->user_name, ENT_QUOTES, 'UTF-8') ?></h4>
                                <span class="text-xs text-gray-400"><?= date('M j, Y g:i A', strtotime($comment->created_at)) ?></span>
                            </div>
                            <p class="text-sm text-gray-700 whitespace-pre-wrap pr-6"><?= htmlspecialchars($comment->comment_text, ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>