<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Get teacher user
        $teacher = User::where('email', 'teacher@sempat.test')->first();
        
        // Get some lessons to attach quizzes to
        $lessons = Lesson::where('type', 'text')->take(10)->get();

        if ($lessons->isEmpty()) {
            $this->command->warn('No lessons found. Please run CourseSeeder first.');
            return;
        }

        $this->command->info('Creating quizzes...');

        // Quiz 1: HTML Basics Quiz
        $quiz1 = Quiz::create([
            'lesson_id' => $lessons[0]->id,
            'created_by' => $teacher->id,
            'title' => 'HTML Basics Quiz',
            'description' => 'Test your understanding of HTML fundamentals.',
            'instructions' => 'Please read each question carefully and select the best answer. You have 15 minutes to complete this quiz.',
            'time_limit_minutes' => 15,
            'passing_score' => 70,
            'max_attempts' => 3,
            'show_correct_answers' => true,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'status' => 'published',
            'published_at' => now(),
            'total_questions' => 5,
        ]);

        // Questions for Quiz 1
        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'multiple_choice',
            'question' => 'What does HTML stand for?',
            'options' => [
                'Hyper Text Markup Language',
                'High Tech Modern Language',
                'Home Tool Markup Language',
                'Hyperlinks and Text Markup Language'
            ],
            'correct_answer' => 'A',
            'explanation' => 'HTML stands for Hyper Text Markup Language. It is the standard markup language for creating web pages.',
            'points' => 1,
            'order' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'multiple_choice',
            'question' => 'Which HTML tag is used to create a hyperlink?',
            'options' => [
                '<link>',
                '<a>',
                '<href>',
                '<url>'
            ],
            'correct_answer' => 'B',
            'explanation' => 'The <a> tag (anchor tag) is used to create hyperlinks in HTML.',
            'points' => 1,
            'order' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'true_false',
            'question' => 'HTML is a programming language.',
            'options' => null,
            'correct_answer' => 'false',
            'explanation' => 'HTML is a markup language, not a programming language. It is used to structure content on the web.',
            'points' => 1,
            'order' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'multiple_choice',
            'question' => 'Which tag is used to display an image in HTML?',
            'options' => [
                '<picture>',
                '<img>',
                '<image>',
                '<src>'
            ],
            'correct_answer' => 'B',
            'explanation' => 'The <img> tag is used to embed images in an HTML page.',
            'points' => 1,
            'order' => 4,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz1->id,
            'type' => 'short_answer',
            'question' => 'What is the correct HTML tag for the largest heading?',
            'options' => null,
            'correct_answer' => 'h1',
            'explanation' => 'The <h1> tag defines the most important heading. HTML headings are defined with <h1> to <h6> tags.',
            'points' => 2,
            'order' => 5,
        ]);

        // Quiz 2: CSS Fundamentals
        $quiz2 = Quiz::create([
            'lesson_id' => $lessons[1]->id,
            'created_by' => $teacher->id,
            'title' => 'CSS Fundamentals Quiz',
            'description' => 'Evaluate your CSS knowledge.',
            'instructions' => 'Answer all questions to the best of your ability. No time limit.',
            'time_limit_minutes' => null,
            'passing_score' => 75,
            'max_attempts' => 0,
            'show_correct_answers' => true,
            'shuffle_questions' => false,
            'shuffle_options' => true,
            'status' => 'published',
            'published_at' => now(),
            'total_questions' => 6,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'multiple_choice',
            'question' => 'What does CSS stand for?',
            'options' => [
                'Creative Style Sheets',
                'Cascading Style Sheets',
                'Computer Style Sheets',
                'Colorful Style Sheets'
            ],
            'correct_answer' => 'B',
            'explanation' => 'CSS stands for Cascading Style Sheets.',
            'points' => 1,
            'order' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'multiple_choice',
            'question' => 'Which property is used to change the background color in CSS?',
            'options' => [
                'color',
                'bgcolor',
                'background-color',
                'bg-color'
            ],
            'correct_answer' => 'C',
            'explanation' => 'The background-color property is used to set the background color of an element.',
            'points' => 1,
            'order' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'true_false',
            'question' => 'CSS can be included inline within HTML tags.',
            'options' => null,
            'correct_answer' => 'true',
            'explanation' => 'CSS can be added inline using the style attribute in HTML tags.',
            'points' => 1,
            'order' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'multiple_choice',
            'question' => 'How do you select an element with id "header" in CSS?',
            'options' => [
                '.header',
                '#header',
                'header',
                '*header'
            ],
            'correct_answer' => 'B',
            'explanation' => 'The # symbol is used to select elements by their ID in CSS.',
            'points' => 1,
            'order' => 4,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'short_answer',
            'question' => 'Which CSS property controls the text size?',
            'options' => null,
            'correct_answer' => 'font-size',
            'explanation' => 'The font-size property is used to set the size of text.',
            'points' => 2,
            'order' => 5,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz2->id,
            'type' => 'essay',
            'question' => 'Explain the difference between inline, internal, and external CSS. Give an example of when you would use each.',
            'options' => null,
            'correct_answer' => '',
            'explanation' => 'This is a subjective question that requires manual grading.',
            'points' => 3,
            'order' => 6,
        ]);

        // Quiz 3: JavaScript Basics
        $quiz3 = Quiz::create([
            'lesson_id' => $lessons[2]->id,
            'created_by' => $teacher->id,
            'title' => 'JavaScript Fundamentals',
            'description' => 'Test your JavaScript knowledge.',
            'instructions' => 'Complete all questions. You have 20 minutes.',
            'time_limit_minutes' => 20,
            'passing_score' => 70,
            'max_attempts' => 2,
            'show_correct_answers' => true,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'status' => 'published',
            'published_at' => now(),
            'total_questions' => 5,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz3->id,
            'type' => 'multiple_choice',
            'question' => 'Which keyword is used to declare a variable in JavaScript?',
            'options' => [
                'var',
                'let',
                'const',
                'All of the above'
            ],
            'correct_answer' => 'D',
            'explanation' => 'In JavaScript, variables can be declared using var, let, or const.',
            'points' => 1,
            'order' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz3->id,
            'type' => 'true_false',
            'question' => 'JavaScript and Java are the same language.',
            'options' => null,
            'correct_answer' => 'false',
            'explanation' => 'JavaScript and Java are completely different programming languages with different purposes.',
            'points' => 1,
            'order' => 2,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz3->id,
            'type' => 'multiple_choice',
            'question' => 'What is the output of: console.log(typeof null)?',
            'options' => [
                'null',
                'undefined',
                'object',
                'number'
            ],
            'correct_answer' => 'C',
            'explanation' => 'In JavaScript, typeof null returns "object". This is a known quirk in the language.',
            'points' => 2,
            'order' => 3,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz3->id,
            'type' => 'short_answer',
            'question' => 'What method is used to add an element to the end of an array?',
            'options' => null,
            'correct_answer' => 'push',
            'explanation' => 'The push() method adds one or more elements to the end of an array.',
            'points' => 2,
            'order' => 4,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz3->id,
            'type' => 'multiple_choice',
            'question' => 'Which symbol is used for single-line comments in JavaScript?',
            'options' => [
                '//',
                '/* */',
                '#',
                '--'
            ],
            'correct_answer' => 'A',
            'explanation' => '// is used for single-line comments in JavaScript.',
            'points' => 1,
            'order' => 5,
        ]);

        $this->command->info('✓ Created 3 quizzes with total 16 questions');
    }
}
