# Core\SubjectList

This class manage a list of all subjects ([design pattern observer](https://en.wikipedia.org/wiki/Observer_pattern))
which are declared (the addition is not automatic).

## Constants

### Errors/Exception codes

These constants are sent when an exception is thrown and used like exception code.

__`ERR_SUBJECT_NAME_NOT_EXIST`__

Exception code if a subject name is not found.

__`ERR_SUBJECT_NOT_FOUND`__

Exception code if a subject is not found.

__`ERR_ADD_SUBJECT_ALREADY_EXIST`__

Exception code if during the addition of a new subject, a subject into the list already has the defined name.

## Property

__`protected \SplSubject[] $subjectList = [];`__

List of all subjects declared

## Methods

### Getters

__`\SplSubject[] public getSubjectList()`__

Getter to the property `$subjectList` which contains the list of all subjects.

__`\SplSubject public getSubjectByName(string $subjectName)`__

Getter to the specific subject `$subjectName` in the list on the property `$subjectList`.

If the `$subjectName` not exist in the list, an exception will be thrown;
the exception code will be the constant `\BFW\Core\SubjectList::ERR_SUBJECT_NAME_NOT_EXIST`.

### Manage list

__`self public addSubject(\SplSubject $subject, [string|null $subjectName=null])`__

Add the subject `$subject` to the list on the property `$subjectList` with `$subjectName` as key.

If `$subjectName` is `null`, this value will be changed to the class name of `$subject`.

If `$subjectName` already exist in the list but for a different subject, an exception will be thrown;
the exception code will be the constant `\BFW\Core\SubjectList::ERR_ADD_SUBJECT_ALREADY_EXIST`.

__`self public removeSubject(\SplSubject $subject)`__

Remove the subject `$subject` from the list.

If `$subject` is not found in the list, an exception will be thrown;
the exception code will be the constant `\BFW\Core\SubjectList::ERR_SUBJECT_NOT_FOUND`.
